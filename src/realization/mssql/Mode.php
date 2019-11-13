<?php

namespace fize\db\realization\mssql;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\mssql\mode\Adodb;
use fize\db\realization\mssql\mode\Odbc;
use fize\db\realization\mssql\mode\Pdo;
use fize\db\realization\mssql\mode\Sqlsrv;
use fize\db\exception\Exception;


/**
 * MSSQL的ORM模型
 */
class Mode implements ModeInterface
{

    /**
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 数据库名
     * @param mixed $port 端口号，MSSQL默认是1433
     * @param string $driver 指定ADODB驱动名称。
     * @return Adodb
     */
    public static function adodb($host, $user, $pwd, $dbname, $port = "", $driver = null)
    {
        return new Adodb($host, $user, $pwd, $dbname, $port, $driver);
    }

    /**
     * odbc方式构造
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 数据库名
     * @param mixed $port 端口号，选填，MSSQL默认是1433
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($host, $user, $pwd, $dbname, $port = "", $driver = null)
    {
        return new Odbc($host, $user, $pwd, $dbname, $port, $driver);
    }

    /**
     * sqlsrv方式构造
     *
     * 微软官方支持，可以放心使用
     * @param string $host 数据库服务器
     * @param string $user 数据库登录账户
     * @param string $pwd 数据库登录密码
     * @param string $dbname 数据库名
     * @param mixed $port 数据库服务器端口，选填，默认是1433(默认设置)
     * @param string $charset 指定数据库编码，默认GBK,(不区分大小写)
     * @return Sqlsrv
     */
    public static function sqlsrv($host, $user, $pwd, $dbname, $port = "", $charset = "GBK")
    {
        return new Sqlsrv($host, $user, $pwd, $dbname, $port, $charset);
    }

    /**
     * Pdo方式构造
     *
     * 强烈推荐使用
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 数据库名
     * @param string $port 端口号，选填，MSSQL默认是1433
     * @param string $charset 指定编码，选填，默认GBK,(不区分大小写)
     * @param array $opts PDO连接的其他选项，选填
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $port = "", $charset = "GBK", array $opts = [])
    {
        return new Pdo($host, $user, $pwd, $dbname, $port, $charset, $opts);
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
            'prefix'      => '',
            'new_feature' => true,
            'driver'      => null,
            'charset'     => 'GBK',
            'opts'        => []
        ];
        $dbcfg = array_merge($default_dbcfg, $dbcfg);
        switch ($mode) {
            case 'adodb':
                $db = self::adodb($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['driver']);
                break;
            case 'odbc':
                $db = self::odbc($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['driver']);
                break;
            case 'pdo':
                $db = self::pdo($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['charset'], $dbcfg['opts']);
                break;
            case 'sqlsrv':
                $db = self::sqlsrv($dbcfg['host'], $dbcfg['user'], $dbcfg['password'], $dbcfg['dbname'], $dbcfg['port'], $dbcfg['charset']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($dbcfg['prefix']);
        $db->newFeature($dbcfg['new_feature']);
        return $db;
    }
}