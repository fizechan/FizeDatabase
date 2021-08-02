<?php

namespace fize\database\extend\mysql;

use fize\database\extend\mysql\mode\Odbc;
use fize\database\extend\mysql\mode\Mysqli;
use fize\database\extend\mysql\mode\Pdo;

/**
 * 模式
 *
 * MySQL数据库模型类
 */
class Mode
{

    /**
     * mysqli方式构造
     * @notice mysqli最终还是会被淘汰的，建议谨慎使用
     * @param string      $host    服务器地址
     * @param string      $user    用户名
     * @param string      $pwd     用户密码
     * @param string      $dbname  指定数据库
     * @param mixed       $port    端口号，MySQL默认是3306
     * @param string      $charset 指定编码，选填，默认utf8
     * @param array       $opts    设置MYSQL连接选项
     * @param bool        $real    是否使用real方式，默认true
     * @param string|null $socket  指定应使用的套接字或命名管道，选填，默认不指定
     * @param array       $ssl_set 设置SSL选项，选填，为数组参数，其下有参数ENABLE、KEY、CERT、CA、CAPATH、CIPHER，如果ENABLE为true，则其余参数都需要填写
     * @param int|null    $flags   设置连接参数，选填，如MYSQLI_CLIENT_SSL等
     * @return Mysqli
     */
    public static function mysqli(string $host, string $user, string $pwd, string $dbname, $port = "", string $charset = "utf8", array $opts = [], bool $real = true, string $socket = null, array $ssl_set = [], int $flags = null): Mysqli
    {
        return new Mysqli($host, $user, $pwd, $dbname, $port, $charset, $opts, $real, $socket, $ssl_set, $flags);
    }

    /**
     * odbc方式构造
     * @notice ODBC本身未实现数据库特性，仅适用于一般性调用
     * @param string      $host    服务器地址
     * @param string      $user    用户名
     * @param string      $pwd     用户密码
     * @param string      $dbname  数据库名
     * @param mixed       $port    端口号，选填，MySQL默认是3306
     * @param string      $charset 指定编码，选填，默认utf8
     * @param string|null $driver  指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc(string $host, string $user, string $pwd, string $dbname, $port = "", string $charset = "utf8", string $driver = null): Odbc
    {
        return new Odbc($host, $user, $pwd, $dbname, $port, $charset, $driver);
    }

    /**
     * Pdo方式构造
     *
     * 强烈推荐使用
     * @param string      $host    服务器地址
     * @param string      $user    用户名
     * @param string      $pwd     用户密码
     * @param string      $dbname  数据库名
     * @param int|null    $port    端口号，选填，MySQL默认是3306
     * @param string      $charset 指定编码，选填，默认utf8
     * @param array       $opts    PDO连接的其他选项，选填
     * @param string|null $socket  指定应使用的套接字或命名管道,windows不可用，选填，默认不指定
     * @return Pdo
     */
    public static function pdo(string $host, string $user, string $pwd, string $dbname, int $port = null, string $charset = "utf8", array $opts = [], string $socket = null): Pdo
    {
        return new Pdo($host, $user, $pwd, $dbname, $port, $charset, $opts, $socket);
    }
}
