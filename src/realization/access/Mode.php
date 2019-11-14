<?php

namespace fize\db\realization\access;

use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\access\mode\Adodb;
use fize\db\realization\access\mode\Odbc;
use fize\db\realization\access\mode\Pdo;
use fize\db\exception\Exception;


/**
 * 模式
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Adodb
     */
    public static function adodb($file, $pwd = null, $driver = null)
    {
        return new Adodb($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($file, $pwd = null, $driver = null)
    {
        return new Odbc($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Pdo
     */
    public static function pdo($file, $pwd = null, $driver = null)
    {
        return new Pdo($file, $pwd, $driver);
    }

    /**
     * 数据库实例
     * @param array $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function getInstance(array $config)
    {
        $mode = isset($config['mode']) ? $config['mode'] : 'adodb';
        $dbcfg = $config['config'];
        $default_dbcfg = [
            'password' => null,
            'prefix'   => '',
            'driver'   => null
        ];
        $dbcfg = array_merge($default_dbcfg, $dbcfg);

        switch ($mode) {
            case 'adodb':
                $db = self::adodb($dbcfg['file'], $dbcfg['password'], $dbcfg['driver']);
                break;
            case 'odbc':
                $db = self::odbc($dbcfg['file'], $dbcfg['password'], $dbcfg['driver']);
                break;
            case 'pdo':
                $db = self::pdo($dbcfg['file'], $dbcfg['password'], $dbcfg['driver']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($dbcfg['prefix']);
        return $db;
    }
}