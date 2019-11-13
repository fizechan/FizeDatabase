<?php

namespace fize\db\realization\mysql;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\mysql\mode\Odbc;
use fize\db\realization\mysql\mode\Mysqli;
use fize\db\realization\mysql\mode\Pdo;
use fize\db\exception\Exception;

/**
 * MySQL数据库模型类
 */
class Mode implements ModeInterface
{

    /**
     * mysqli方式构造
     * @notice mysqli最终还是会被淘汰的，建议谨慎使用
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 指定数据库
     * @param mixed $port 端口号，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array $opts 设置MYSQL连接选项
     * @param bool $real 是否使用real方式，默认true
     * @param string $socket 指定应使用的套接字或命名管道，选填，默认不指定
     * @param array $ssl_set 设置SSL选项，选填，为数组参数，其下有参数ENABLE、KEY、CERT、CA、CAPATH、CIPHER，如果ENABLE为true，则其余参数都需要填写
     * @param int $flags 设置连接参数，选填，如MYSQLI_CLIENT_SSL等
     * @return Mysqli
     */
    public static function mysqli($host, $user, $pwd, $dbname, $port = "", $charset = "utf8", array $opts = [], $real = true, $socket = null, array $ssl_set = [], $flags = null)
    {
        return new Mysqli($host, $user, $pwd, $dbname, $port, $charset, $opts, $real, $socket, $ssl_set, $flags);
    }

    /**
     * odbc方式构造
     * @notice ODBC本身未实现数据库特性，仅适用于一般性调用
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 数据库名
     * @param mixed $port 端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($host, $user, $pwd, $dbname, $port = "", $charset = "utf8", $driver = null)
    {
        return new Odbc($host, $user, $pwd, $dbname, $port, $charset, $driver);
    }

    /**
     * Pdo方式构造
     *
     * 强烈推荐使用
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 数据库名
     * @param int $port 端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array $opts PDO连接的其他选项，选填
     * @param string $socket 指定应使用的套接字或命名管道,windows不可用，选填，默认不指定
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $port = null, $charset = "utf8", array $opts = [], $socket = null)
    {
        return new Pdo($host, $user, $pwd, $dbname, $port, $charset, $opts, $socket);
    }

    /**
     * 数据库实例
     * @param array $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function getInstance(array $config)
    {
        $mode = isset($config['mode']) ? $config['mode'] : 'pdo';
        $dbcfg = $config['config'];
        $default_dbcfg = [
            'port'        => '',
            'charset'     => 'utf8',
            'prefix'      => '',
            'opts'        => [],
            'real'        => true,
            'socket'      => null,
            'ssl_set'     => [],
            'flags'       => null,
            'driver'      => null
        ];
        $dbcfg = array_merge($default_dbcfg, $dbcfg);
        switch ($mode) {
            case 'mysqli':
                $db = self::mysqli($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['charset'], $dbcfg['opts'], $dbcfg['real'], $dbcfg['socket'], $dbcfg['ssl_set'], $dbcfg['flags']);
                break;
            case 'odbc':
                $db = self::odbc($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['charset'], $dbcfg['driver']);
                break;
            case 'pdo':
                $db = self::pdo($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['charset'], $dbcfg['opts'], $dbcfg['socket']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($dbcfg['prefix']);
        return $db;
    }
}