<?php

namespace fize\db\extend\pgsql;

use fize\db\extend\pgsql\mode\Odbc;
use fize\db\extend\pgsql\mode\Pgsql;
use fize\db\extend\pgsql\mode\Pdo;

/**
 * 模式
 *
 * PostgreSQL数据库模型类
 */
class Mode
{

    /**
     * odbc方式构造
     * @param string     $host   服务器地址
     * @param string     $user   用户名
     * @param string     $pwd    用户密码
     * @param string     $dbname 数据库名
     * @param string|int $port   端口号，选填，PostgreSQL默认是5432
     * @param string     $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($host, $user, $pwd, $dbname, $port = "", $driver = null)
    {
        return new Odbc($host, $user, $pwd, $dbname, $port, $driver);
    }

    /**
     * pgsql方式构造
     * @param string $connection_string 连接字符串
     * @param bool   $pconnect          是否使用长连接
     * @param int    $connect_type      PGSQL_CONNECT_FORCE_NEW使用新连接
     * @return Pgsql
     */
    public static function pgsql($connection_string, $pconnect = false, $connect_type = null)
    {
        return new Pgsql($connection_string, $pconnect, $connect_type);
    }

    /**
     * Pdo方式构造
     * @param string $host   服务器地址
     * @param string $user   用户名
     * @param string $pwd    用户密码
     * @param string $dbname 数据库名
     * @param int    $port   端口号，选填，PostgreSQL默认是5432
     * @param array  $opts   PDO连接的其他选项，选填
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $port = null, array $opts = [])
    {
        return new Pdo($host, $user, $pwd, $dbname, $port, $opts);
    }
}
