<?php

namespace fize\database\extend\pgsql;

use fize\database\core\ModeFactoryInterface;
use fize\database\exception\Exception;

/**
 * 模式工厂
 */
class ModeFactory implements ModeFactoryInterface
{

    /**
     * 创建实例
     * @param string $mode   连接模式
     * @param array  $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function create(string $mode, array $config): Db
    {
        $mode = $mode ?: 'pdo';
        $default_config = [
            'port'         => '5432',
            'charset'      => 'UTF8',
            'prefix'       => '',
            'driver'       => null,
            'pconnect'     => false,
            'connect_type' => null,
            'opts'         => []
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'odbc':
                $db = Mode::odbc($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['driver']);
                break;
            case 'pgsql':
                $host = $config['host'];
                $port = $config['port'];
                $dbname = $config['dbname'];
                $user = $config['user'];
                $password = $config['password'];
                $connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
                $db = Mode::pgsql($connection_string, $config['pconnect'], $config['connect_type']);
                break;
            case 'pdo':
                $db = Mode::pdo($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['opts']);
                break;
            default:
                throw new Exception("error db mode: $mode");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}
