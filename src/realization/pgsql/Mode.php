<?php

namespace fize\db\realization\pgsql;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\pgsql\mode\Odbc;
use fize\db\realization\pgsql\mode\Pgsql;
use fize\db\realization\pgsql\mode\Pdo;
use fize\db\exception\DbException;

/**
 * PostgreSQL数据库模型类
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @notice ODBC本身未实现数据库特性，仅适用于一般性调用
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
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
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
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
     * @throws DbException
     */
    public static function getInstance(array $config)
    {
        $mode = isset($config['mode']) ? $config['mode'] : 'pgsql';
        $db_cfg = $config['config'];
        $db = null;
        switch ($mode) {
            case 'odbc':
                $port = isset($db_cfg['port']) ? $db_cfg['port'] : '';
                $charset = isset($db_cfg['charset']) ? $db_cfg['charset'] : 'utf8';
                $driver = isset($db_cfg['driver']) ? $db_cfg['driver'] : null;
                $db = self::odbc($db_cfg['host'], $db_cfg['user'], $db_cfg['password'], $db_cfg['dbname'], $port, $charset, $driver);
                break;
            case 'pgsql':
                $host = $db_cfg['host'];
                $port = isset($db_cfg['port']) ? $db_cfg['port'] : '5432';
                $dbname = $db_cfg['dbname'];
                $user = $db_cfg['user'];
                $password = $db_cfg['password'];
                $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";

                $pconnect = isset($db_cfg['pconnect']) ? $db_cfg['pconnect'] : false;
                $connect_type = isset($db_cfg['connect_type']) ? $db_cfg['connect_type'] : null;

                $db = self::pgsql($connection_string, $pconnect, $connect_type);
                break;
            case 'pdo':
                $port = isset($db_cfg['port']) ? $db_cfg['port'] : null;
                $opts = isset($db_cfg['opts']) ? $db_cfg['opts'] : [];
                $db = self::pdo($db_cfg['host'], $db_cfg['user'], $db_cfg['password'], $db_cfg['dbname'], $port, $opts);
                break;
            default:
                throw new DbException("error db mode: {$mode}");
        }
        return $db;
    }
}
