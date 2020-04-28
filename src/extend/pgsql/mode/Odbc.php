<?php

namespace fize\db\extend\pgsql\mode;

use fize\db\extend\pgsql\Db;
use fize\db\middleware\Odbc as Middleware;

/**
 * ODBC
 *
 * ODBC方式PostgreSQL数据库模型类
 */
class Odbc extends Db
{
    use Middleware;

    /**
     * 构造
     * @param string     $host   服务器地址
     * @param string     $user   用户名
     * @param string     $pwd    用户密码
     * @param string     $dbname 数据库名
     * @param string|int $port   端口号，选填，PostgreSQL默认是5432
     * @param string     $driver 指定ODBC驱动名称。
     */
    public function __construct($host, $user, $pwd, $dbname, $port = "", $driver = null)
    {
        if (is_null($driver)) {
            $driver = "{PostgreSQL ANSI}";
            //$driver = "{PostgreSQL UNICODE}";
        }
        $dsn = "DRIVER={$driver};SERVER={$host};DATABASE={$dbname}";
        if (!empty($port)) {
            $dsn .= ";PORT={$port}";
        }
        $this->odbcConstruct($dsn, $user, $pwd, SQL_CUR_USE_ODBC);
        $this->query("SET CLIENT_ENCODING TO 'UTF8'");
    }

    /**
     * 析构时关闭ODBC
     */
    public function __destruct()
    {
        $this->odbcDestruct();
        parent::__destruct();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在PostgreSQL中必须指定
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $sql = "SELECT currval('{$name}')";
        $result = $this->driver->prepare($sql);
        $result->execute();
        $result->fetchArray();
        return $result->result(1);
    }
}
