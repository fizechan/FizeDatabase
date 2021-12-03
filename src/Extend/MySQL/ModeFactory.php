<?php

namespace Fize\Database\Extend\MySQL;

use Fize\Database\Core\ModeFactoryInterface;
use Fize\Exception\DatabaseException;

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
     * @throws DatabaseException
     */
    public static function create(string $mode, array $config): Db
    {
        $mode = $mode ?: 'pdo';
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
                throw new DatabaseException("error db mode: $mode");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}
