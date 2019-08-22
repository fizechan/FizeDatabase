<?php

namespace fize\db\realization\mssql;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\mssql\mode\Odbc;
use fize\db\realization\mssql\mode\Pdo;
use fize\db\realization\mssql\mode\Sqlsrv;
use fize\db\exception\DbException;


/**
 * MSSQL的ORM模型
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @todo ODBC本身未实现数据库特性，仅适用于一般性调用
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param mixed $port 端口号，选填，MySQL默认是3306
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($host, $user, $pwd, $dbname, $prefix = "", $port = "", $driver = null)
    {
        return new Odbc($host, $user, $pwd, $dbname, $prefix, $port, $driver);
    }

    /**
     * sqlsrv方式构造
     * 微软官方支持，可以放心使用
     * @param string $host 数据库服务器
     * @param string $user 数据库登录账户
     * @param string $pwd 数据库登录密码
     * @param string $dbname 数据库名
     * @param string $prefix 指定前缀，选填，默认空字符
     * @param mixed $port 数据库服务器端口，选填，默认是1433(默认设置)
     * @param string $charset 指定数据库编码，默认GBK,(不区分大小写)
     * @return Sqlsrv
     */
    public static function sqlsrv($host, $user, $pwd, $dbname, $prefix = "", $port = "", $charset = "GBK")
    {
        return new Sqlsrv($host, $user, $pwd, $dbname, $prefix, $port, $charset);
    }

    /**
     * Pdo方式构造
     * 强烈推荐使用
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param string $port 端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array $opts PDO连接的其他选项，选填
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $prefix = "", $port = "", $charset = "GBK", array $opts = [])
    {
        return new Pdo($host, $user, $pwd, $dbname, $prefix, $port, $charset, $opts);
    }

    /**
     * 数据库实例
     * @param array $options 数据库参数选项
     * @return Db
     * @throws DbException
     */
    public static function getInstance(array $options)
    {
        $option = $options['option'];
        $db = null;
        switch ($options['mode']) {
            case 'odbc':
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $port = isset($option['port']) ? $option['port'] : '';
                $driver = isset($option['driver']) ? $option['driver'] : null;
                $db = self::odbc($option['host'], $option['user'], $option['password'], $option['dbname'], $prefix, $port, $driver);
                break;
            case 'sqlsrv':
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $port = isset($option['port']) ? $option['port'] : '';
                $charset = isset($option['charset']) ? $option['charset'] : 'GBK';
                $db = self::sqlsrv($option['host'], $option['user'], $option['password'], $option['dbname'], $prefix, $port, $charset);
                break;
            case 'pdo':
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $port = isset($option['port']) ? $option['port'] : '';
                $charset = isset($option['charset']) ? $option['charset'] : 'GBK';
                $opts = isset($option['opts']) ? $option['opts'] : [];
                $db = self::pdo($option['host'], $option['user'], $option['password'], $option['dbname'], $prefix, $port, $charset, $opts);
                break;
            default:
                throw new DbException("error db mode: {$options['mode']}");
        }
        if(isset($option['new_feature'])) {
            $db->newFeature($option['new_feature']);  //开启新特性支持
        }
        return $db;
    }
}