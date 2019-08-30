<?php

namespace fize\db\realization\access;

use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\access\mode\Adodb;
use fize\db\realization\access\mode\Odbc;
use fize\db\realization\access\mode\Pdo;
use fize\db\exception\DbException;


/**
 * Access数据库模型类
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param string $driver 指定ODBC驱动名称。
     * @return Adodb
     */
    public static function adodb($file, $pwd = null, $prefix = "", $driver = null)
    {
        return new Adodb($file, $pwd, $prefix, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($file, $pwd = null, $prefix = "", $driver = null)
    {
        return new Odbc($file, $pwd, $prefix, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param string $driver 指定ODBC驱动名称。
     * @return Pdo
     */
    public static function pdo($file, $pwd = null, $prefix = "", $driver = null)
    {
        return new Pdo($file, $pwd, $prefix, $driver);
    }

    /**
     * 数据库实例
     * @param array $options 数据库参数选项
     * @return Db
     * @throws DbException
     */
    public static function getInstance(array $options)
    {
        $mode = isset($options['mode']) ? $options['mode'] : 'adodb';
        $option = $options['option'];
        switch ($mode) {
            case 'adodb':
                $pwd = isset($option['password']) ? $option['password'] : null;
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $driver = isset($option['driver']) ? $option['driver'] : null;
                return self::adodb($option['file'], $pwd, $prefix, $driver);
            case 'odbc':
                $pwd = isset($option['password']) ? $option['password'] : null;
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $driver = isset($option['driver']) ? $option['driver'] : null;
                return self::odbc($option['file'], $pwd, $prefix, $driver);
            case 'pdo':
                $pwd = isset($option['password']) ? $option['password'] : null;
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $driver = isset($option['driver']) ? $option['driver'] : null;
                return self::pdo($option['file'], $pwd, $prefix, $driver);
            default:
                throw new DbException("error db mode: {$options['mode']}");
        }
    }
}