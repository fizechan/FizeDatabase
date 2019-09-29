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
     * @param array $config 数据库参数选项
     * @return Db
     * @throws DbException
     */
    public static function getInstance(array $config)
    {
        $mode = isset($config['mode']) ? $config['mode'] : 'adodb';
        $db_cfg = $config['config'];
        switch ($mode) {
            case 'adodb':
                $pwd = isset($db_cfg['password']) ? $db_cfg['password'] : null;
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $driver = isset($db_cfg['driver']) ? $db_cfg['driver'] : null;
                return self::adodb($db_cfg['file'], $pwd, $prefix, $driver);
            case 'odbc':
                $pwd = isset($db_cfg['password']) ? $db_cfg['password'] : null;
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $driver = isset($db_cfg['driver']) ? $db_cfg['driver'] : null;
                return self::odbc($db_cfg['file'], $pwd, $prefix, $driver);
            case 'pdo':
                $pwd = isset($db_cfg['password']) ? $db_cfg['password'] : null;
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $driver = isset($db_cfg['driver']) ? $db_cfg['driver'] : null;
                return self::pdo($db_cfg['file'], $pwd, $prefix, $driver);
            default:
                throw new DbException("error db mode: {$mode}");
        }
    }
}