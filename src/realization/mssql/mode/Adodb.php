<?php


namespace fize\db\realization\mssql\mode;

use fize\db\realization\mssql\Db;
use fize\db\middleware\Adodb as Middleware;

/**
 * ADODB方式连接MSSQL数据库
 * @package fize\db\realization\mssql\mode
 */
class Adodb extends Db
{
    use Middleware;

    /**
     * 构造时创建Adodb连接
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param mixed $port 端口号，选填，MySQL默认是1344
     * @param string $driver 指定ADODB驱动名称。
     */
    public function __construct($host, $user, $pwd, $dbname, $port = "", $driver = null)
    {
        if (is_null($driver)) {
            $driver = "SQLNCLI11";
        }
        $dsn = "Provider={$driver};Server=" . $host;
        if($port) {
            $dsn .= ":{$port}";
        }
        $dsn .= ";Database={$dbname};Uid={$user};Pwd={$pwd}";
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