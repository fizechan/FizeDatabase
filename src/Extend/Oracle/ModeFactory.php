<?php

namespace Fize\Database\Extend\Oracle;

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
     * @param array  $config 数据库参数选项
     * @return Db
     * @throws DatabaseException
     */
    public static function create(string $mode, array $config): Db
    {
        $mode = $mode ?: 'pdo';
        $default_config = [
            'port'         => '',
            'charset'      => 'UTF8',
            'prefix'       => '',
            'session_mode' => null,
            'connect_type' => 1,
            'opts'         => [],
            'driver'       => null
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'oci':
                $connection_string = $config['host'];
                if ($config['port']) {
                    $connection_string .= ':' . $config['port'];
                }
                $connection_string .= '/' . $config['dbname'];
                $db = Mode::oci(
                    $config['username'],
                    $config['password'],
                    $connection_string,
                    $config['charset'],
                    $config['session_mode'],
                    $config['connect_type']
                );
                break;
            case 'odbc':
                $sid = $config['host'];
                if ($config['port']) {
                    $sid .= ':' . $config['port'];
                }
                $sid .= '/' . $config['dbname'];
                $db = Mode::odbc($config['username'], $config['password'], $sid, $config['port'], $config['charset'], $config['driver']);
                break;
            case 'pdo':
                $db = Mode::pdo(
                    $config['host'],
                    $config['user'],
                    $config['password'],
                    $config['dbname'],
                    $config['port'],
                    $config['charset'],
                    $config['opts']
                );
                break;
            default:
                throw new DatabaseException("error db mode: $mode");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}
