<?php

namespace fize\db\extend\access;

use fize\db\core\Mode as ModeInterface;
use fize\db\exception\Exception;
use fize\db\extend\access\mode\Adodb;
use fize\db\extend\access\mode\Odbc;
use fize\db\extend\access\mode\Pdo;

/**
 * 模式
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @param string $file   Access文件路径
     * @param string $pwd    用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Adodb
     */
    public static function adodb($file, $pwd = null, $driver = null)
    {
        return new Adodb($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file   Access文件路径
     * @param string $pwd    用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($file, $pwd = null, $driver = null)
    {
        return new Odbc($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file   Access文件路径
     * @param string $pwd    用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Pdo
     */
    public static function pdo($file, $pwd = null, $driver = null)
    {
        return new Pdo($file, $pwd, $driver);
    }

    /**
     * 数据库实例
     * @param string $mode   连接模式
     * @param array  $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function create($mode, array $config)
    {
        $mode = $mode ? $mode : 'adodb';
        $default_config = [
            'password' => null,
            'prefix'   => '',
            'driver'   => null
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'adodb':
                $db = self::adodb($config['file'], $config['password'], $config['driver']);
                break;
            case 'odbc':
                $db = self::odbc($config['file'], $config['password'], $config['driver']);
                break;
            case 'pdo':
                $db = self::pdo($config['file'], $config['password'], $config['driver']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}
