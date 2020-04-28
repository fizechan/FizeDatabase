<?php

namespace fize\db\extend\access\mode;

use fize\db\extend\access\Db;
use fize\db\middleware\Adodb as Middleware;

/**
 * ADODB
 *
 * (推荐使用)ADODB方式连接access数据库
 */
class Adodb extends Db
{

    use Middleware;

    /**
     * 构造时创建Adodb连接
     * @see https://www.connectionstrings.com/ace-oledb-12-0/
     * @param string $file   数据库文件路径
     * @param string $pwd    密码
     * @param string $driver 驱动名
     */
    public function __construct($file, $pwd = null, $driver = null)
    {
        if (is_null($driver)) {
            $driver = "Microsoft.ACE.OLEDB.12.0";
        }
        $dsn = "Provider={$driver};Data Source=" . realpath($file) . ";";
        if ($pwd) {
            $dsn .= "Jet OLEDB:Database Password=" . $pwd . ";";
        }
        $this->adodbConstruct($dsn, 65001);
    }

    /**
     * 析构时释放ADODB资源
     */
    public function __destruct()
    {
        $this->adodbDestruct();
        parent::__destruct();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在access中无效
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $sql = 'SELECT @@IDENTITY AS id';
        $rows = $this->query($sql);
        return $rows[0]['id'];
    }
}
