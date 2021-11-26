<?php

namespace Fize\Database\Extend\MSSQL;

use Fize\Database\Extend\MSSQL\Mode\ADODB;
use Fize\Database\Extend\MSSQL\Mode\ODBC;
use Fize\Database\Extend\MSSQL\Mode\PDO;
use Fize\Database\Extend\MSSQL\Mode\SQLSRV;

/**
 * 模式
 */
class Mode
{

    /**
     * @param string      $host   服务器地址
     * @param string      $user   用户名
     * @param string      $pwd    用户密码
     * @param string      $dbname 数据库名
     * @param mixed       $port   端口号，MSSQL默认是1433
     * @param string|null $driver 指定ADODB驱动名称。
     * @return ADODB
     */
    public static function adodb(string $host, string $user, string $pwd, string $dbname, $port = "", string $driver = null): ADODB
    {
        return new ADODB($host, $user, $pwd, $dbname, $port, $driver);
    }

    /**
     * odbc方式构造
     * @param string      $host   服务器地址
     * @param string      $user   用户名
     * @param string      $pwd    用户密码
     * @param string      $dbname 数据库名
     * @param mixed       $port   端口号，选填，MSSQL默认是1433
     * @param string|null $driver 指定ODBC驱动名称。
     * @return ODBC
     */
    public static function odbc(string $host, string $user, string $pwd, string $dbname, $port = "", string $driver = null): ODBC
    {
        return new ODBC($host, $user, $pwd, $dbname, $port, $driver);
    }

    /**
     * sqlsrv方式构造
     *
     * 微软官方支持，可以放心使用
     * @param string $host    数据库服务器
     * @param string $user    数据库登录账户
     * @param string $pwd     数据库登录密码
     * @param string $dbname  数据库名
     * @param mixed  $port    数据库服务器端口，选填，默认是1433(默认设置)
     * @param string $charset 指定数据库编码，默认GBK,(不区分大小写)
     * @return SQLSRV
     */
    public static function sqlsrv(string $host, string $user, string $pwd, string $dbname, $port = "", string $charset = "GBK"): SQLSRV
    {
        return new SQLSRV($host, $user, $pwd, $dbname, $port, $charset);
    }

    /**
     * Pdo方式构造
     *
     * 强烈推荐使用
     * @param string $host    服务器地址
     * @param string $user    用户名
     * @param string $pwd     用户密码
     * @param string $dbname  数据库名
     * @param string $port    端口号，选填，MSSQL默认是1433
     * @param string $charset 指定编码，选填，默认GBK,(不区分大小写)
     * @param array  $opts    PDO连接的其他选项，选填
     * @return PDO
     */
    public static function pdo(string $host, string $user, string $pwd, string $dbname, string $port = "", string $charset = "GBK", array $opts = []): PDO
    {
        return new PDO($host, $user, $pwd, $dbname, $port, $charset, $opts);
    }
}
