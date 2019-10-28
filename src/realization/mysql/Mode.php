<?php

namespace fize\db\realization\mysql;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\mysql\mode\Adodb;
use fize\db\realization\mysql\mode\Odbc;
use fize\db\realization\mysql\mode\Mysqli;
use fize\db\realization\mysql\mode\Pdo;
use fize\db\exception\Exception;

/**
 * MySQL数据库模型类
 */
class Mode implements ModeInterface
{

    public static function adodb()
    {
        return new Adodb();
    }

    /**
     * odbc方式构造
     * @notice ODBC本身未实现数据库特性，仅适用于一般性调用
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param mixed $port 端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($host, $user, $pwd, $dbname, $prefix = "", $port = "", $charset = "utf8", $driver = null)
    {
        return new Odbc($host, $user, $pwd, $dbname, $prefix, $port, $charset, $driver);
    }

    /**
     * mysqli方式构造
     * @notice mysqli最终还是会被淘汰的，建议谨慎使用
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 指定数据库
     * @param string $prefix 指定全局前缀
     * @param mixed $port 端口号，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array $opts 设置MYSQL连接选项
     * @param bool $real 是否使用real方式，默认true
     * @param string $socket 指定应使用的套接字或命名管道，选填，默认不指定
     * @param array $ssl_set 设置SSL选项，选填，为数组参数，其下有参数ENABLE、KEY、CERT、CA、CAPATH、CIPHER，如果ENABLE为true，则其余参数都需要填写
     * @param int $flags 设置连接参数，选填，如MYSQLI_CLIENT_SSL等
     * @return Mysqli
     */
    public static function mysqli($host, $user, $pwd, $dbname, $prefix = "", $port = "", $charset = "utf8", array $opts = [], $real = true, $socket = null, array $ssl_set = [], $flags = null)
    {
        return new Mysqli($host, $user, $pwd, $dbname, $prefix, $port, $charset, $opts, $real, $socket, $ssl_set, $flags);
    }

    /**
     * Pdo方式构造
     * 强烈推荐使用
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param int $port 端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array $opts PDO连接的其他选项，选填
     * @param string $socket 指定应使用的套接字或命名管道,windows不可用，选填，默认不指定
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $prefix = "", $port = null, $charset = "utf8", array $opts = [], $socket = null)
    {
        return new Pdo($host, $user, $pwd, $dbname, $prefix, $port, $charset, $opts, $socket);
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
        $db_cfg = $config['config'];
        switch ($mode) {
            case 'adodb':
                return self::adodb();  //todo
            case 'odbc':
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $port = isset($db_cfg['port']) ? $db_cfg['port'] : '';
                $charset = isset($db_cfg['charset']) ? $db_cfg['charset'] : 'utf8';
                $driver = isset($db_cfg['driver']) ? $db_cfg['driver'] : null;
                return self::odbc($db_cfg['host'], $db_cfg['user'], $db_cfg['password'], $db_cfg['dbname'], $prefix, $port, $charset, $driver);
            case 'mysqli':
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $port = isset($db_cfg['port']) ? $db_cfg['port'] : '';
                $charset = isset($db_cfg['charset']) ? $db_cfg['charset'] : 'utf8';
                $opts = isset($db_cfg['opts']) ? $db_cfg['opts'] : [];
                $real = isset($db_cfg['real']) ? $db_cfg['real'] : true;
                $socket = isset($db_cfg['socket']) ? $db_cfg['socket'] : null;
                $ssl_set = isset($db_cfg['ssl_set']) ? $db_cfg['ssl_set'] : [];
                $flags = isset($db_cfg['flags']) ? $db_cfg['flags'] : null;
                return self::mysqli($db_cfg['host'], $db_cfg['user'], $db_cfg['password'], $db_cfg['dbname'], $prefix, $port, $charset, $opts, $real, $socket, $ssl_set, $flags);
            case 'pdo':
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $port = isset($db_cfg['port']) ? $db_cfg['port'] : '';
                $charset = isset($db_cfg['charset']) ? $db_cfg['charset'] : 'utf8';
                $opts = isset($db_cfg['opts']) ? $db_cfg['opts'] : [];
                $socket = isset($db_cfg['socket']) ? $db_cfg['socket'] : null;
                return self::pdo($db_cfg['host'], $db_cfg['user'], $db_cfg['password'], $db_cfg['dbname'], $prefix, $port, $charset, $opts, $socket);
            default:
                throw new Exception("error db mode: {$mode}");
        }
    }
}