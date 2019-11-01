<?php

namespace fize\db\realization\pgsql;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\pgsql\mode\Odbc;
use fize\db\realization\pgsql\mode\Pgsql;
use fize\db\realization\pgsql\mode\Pdo;
use fize\db\exception\Exception;

/**
 * PostgreSQL数据库模型类
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 数据库名
     * @param string|int $port 端口号，选填，PostgreSQL默认是5432
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($host, $user, $pwd, $dbname, $port = "", $driver = null)
    {
        return new Odbc($host, $user, $pwd, $dbname, $port, $driver);
    }

    /**
     * pgsql方式构造
     * @param string $connection_string 连接字符串
     * @param bool $pconnect 是否使用长连接
     * @param int $connect_type PGSQL_CONNECT_FORCE_NEW使用新连接
     * @return Pgsql
     */
    public static function pgsql($connection_string, $pconnect = false, $connect_type = null)
    {
        return new Pgsql($connection_string, $pconnect, $connect_type);
    }

    /**
     * Pdo方式构造
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 数据库名
     * @param int $port 端口号，选填，PostgreSQL默认是5432
     * @param array $opts PDO连接的其他选项，选填
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $port = null, array $opts = [])
    {
        return new Pdo($host, $user, $pwd, $dbname, $port, $opts);
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
            'port'         => '5432',
            'charset'      => 'UTF8',
            'prefix'       => '',
            'driver'       => null,
            'pconnect'     => false,
            'connect_type' => null,
            'opts'         => []
        ];
        $dbcfg = array_merge($default_dbcfg, $dbcfg);
        switch ($mode) {
            case 'odbc':
                $db = self::odbc($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['driver']);
                break;
            case 'pgsql':
                $host = $dbcfg['host'];
                $port = $dbcfg['port'];
                $dbname = $dbcfg['dbname'];
                $user = $dbcfg['user'];
                $password = $dbcfg['password'];
                $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";
                $db = self::pgsql($connection_string, $dbcfg['pconnect'], $dbcfg['connect_type']);
                break;
            case 'pdo':
                $db = self::pdo($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['opts']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($dbcfg['prefix']);
        return $db;
    }
}
