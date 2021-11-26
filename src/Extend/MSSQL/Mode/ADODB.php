<?php


namespace Fize\Database\Extend\MSSQL\Mode;

use Fize\Database\Extend\MSSQL\Db;
use Fize\Database\Middleware\ADODB as Middleware;

/**
 * ADODB
 *
 * ADODB方式连接MSSQL数据库
 */
class ADODB extends Db
{
    use Middleware;

    /**
     * 构造时创建Adodb连接
     * @param string      $host   服务器地址
     * @param string      $user   用户名
     * @param string      $pwd    用户密码
     * @param string      $dbname 数据库名
     * @param mixed       $port   端口号，选填，MSSQL默认是1433
     * @param string|null $driver 指定ADODB驱动名称。
     */
    public function __construct(string $host, string $user, string $pwd, string $dbname, $port = "", string $driver = null)
    {
        if (is_null($driver)) {
            $driver = "SQLNCLI11";  //速度快
            //$driver = "sqloledb";  //最低兼容
        }
        $dsn = "Provider=$driver;Server=" . $host;
        if ($port) {
            $dsn .= ":$port";
        }
        $dsn .= ";Database=$dbname;Uid=$user;Pwd=$pwd";
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
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在access中无效
     * @return int|string
     */
    public function lastInsertId(string $name = null)
    {
        $sql = 'SELECT @@IDENTITY AS id';
        $rows = $this->query($sql);
        return $rows[0]['id'];
    }
}
