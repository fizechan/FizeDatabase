<?php

namespace fize\database\extend\mysql;

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
     * @param array  $config 参数选项
     * @return Db
     * @throws Exception
     */
    public static function create($mode, array $config)
    {
        $mode = $mode ? $mode : 'pdo';
        $default_config = [
            'port'    => '',
            'charset' => 'utf8',
            'prefix'  => '',
            'opts'    => [],
            'real'    => true,
            'socket'  => null,
            'ssl_set' => [],
            'flags'   => null,
            'driver'  => null
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'mysqli':
                $db = Mode::mysqli(
                    $config['host'],
                    $config['user'],
                    $config['password'],
                    $config['dbname'],
                    $config['port'],
                    $config['charset'],
                    $config['opts'],
                    $config['real'],
                    $config['socket'],
                    $config['ssl_set'],
                    $config['flags']
                );
                break;
            case 'odbc':
                $db = Mode::odbc(
                    $config['host'],
                    $config['user'],
                    $config['password'],
                    $config['dbname'],
                    $config['port'],
                    $config['charset'],
                    $config['driver']
                );
                break;
            case 'pdo':
                $db = Mode::pdo(
                    $config['host'],
                    $config['user'],
                    $config['password'],
                    $config['dbname'],
                    $config['port'],
                    $config['charset'],
                    $config['opts'],
                    $config['socket']
                );
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}
