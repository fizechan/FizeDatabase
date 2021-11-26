<?php

namespace Fize\Database\Extend\PgSQL;

use Fize\Database\Extend\PgSQL\Mode\ODBC;
use Fize\Database\Extend\PgSQL\Mode\PDO;
use Fize\Database\Extend\PgSQL\Mode\PgSQL;

/**
 * 模式
 *
 * PostgreSQL数据库模型类
 */
class Mode
{

    /**
     * odbc方式构造
     * @param string      $host   服务器地址
     * @param string      $user   用户名
     * @param string      $pwd    用户密码
     * @param string      $dbname 数据库名
     * @param string|int  $port   端口号，选填，PostgreSQL默认是5432
     * @param string|null $driver 指定ODBC驱动名称。
     * @return ODBC
     */
    public static function odbc(string $host, string $user, string $pwd, string $dbname, $port = "", string $driver = null): ODBC
    {
        return new ODBC($host, $user, $pwd, $dbname, $port, $driver);
    }

    /**
     * Pdo方式构造
     * @param string   $host   服务器地址
     * @param string   $user   用户名
     * @param string   $pwd    用户密码
     * @param string   $dbname 数据库名
     * @param int|null $port   端口号，选填，PostgreSQL默认是5432
     * @param array    $opts   PDO连接的其他选项，选填
     * @return PDO
     */
    public static function pdo(string $host, string $user, string $pwd, string $dbname, int $port = null, array $opts = []): PDO
    {
        return new PDO($host, $user, $pwd, $dbname, $port, $opts);
    }

    /**
     * pgsql方式构造
     * @param string   $connection_string 连接字符串
     * @param bool     $pconnect          是否使用长连接
     * @param int|null $connect_type      PGSQL_CONNECT_FORCE_NEW使用新连接
     * @return PgSQL
     */
    public static function pgsql(string $connection_string, bool $pconnect = false, int $connect_type = null): PgSQL
    {
        return new PgSQL($connection_string, $pconnect, $connect_type);
    }
}
