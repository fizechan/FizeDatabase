<?php

namespace Fize\Database\Extend\SQLite;

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
            'prefix'         => '',
            'long_names'     => 0,
            'time_out'       => 1000,
            'no_txn'         => 0,
            'sync_pragma'    => 'NORMAL',
            'step_api'       => 0,
            'driver'         => null,
            'flags'          => 2,
            'encryption_key' => null,
            'busy_timeout'   => 30000
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'odbc':
                $db = Mode::odbc(
                    $config['file'],
                    $config['long_names'],
                    $config['time_out'],
                    $config['no_txn'],
                    $config['sync_pragma'],
                    $config['step_api'],
                    $config['driver']
                );
                break;
            case 'sqlite3':
                $db = Mode::sqlite3($config['file'], $config['flags'], $config['encryption_key'], $config['busy_timeout']);
                break;
            case 'pdo':
                $db = Mode::pdo($config['file']);
                break;
            default:
                throw new DatabaseException("error db mode: $mode");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}
