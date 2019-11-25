<?php

namespace fize\db\realization\mssql;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\mssql\mode\Adodb;
use fize\db\realization\mssql\mode\Odbc;
use fize\db\realization\mssql\mode\Pdo;
use fize\db\realization\mssql\mode\Sqlsrv;
use fize\db\exception\Exception;


/**
 * 模式
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
     * @param string $mode 连接模式
     * @param array $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function getInstance($mode, array $config)
    {
        $mode = $mode ? $mode : 'pdo';
        $default_config = [
            'port'        => '',
            'prefix'      => '',
            'new_feature' => true,
            'driver'      => null,
            'charset'     => 'GBK',
            'opts'        => []
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'adodb':
                $db = self::adodb($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['driver']);
                break;
            case 'odbc':
                $db = self::odbc($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['driver']);
                break;
            case 'pdo':
                $db = self::pdo($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['charset'], $config['opts']);
                break;
            case 'sqlsrv':
                $db = self::sqlsrv($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['charset']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($config['prefix']);
        $db->newFeature($config['new_feature']);
        return $db;
    }
}