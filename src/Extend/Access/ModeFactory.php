<?php


namespace Fize\Database\Extend\Access;


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
    public static function create(string $mode, array $config)
    {
        $mode = $mode ?: 'adodb';
        $default_config = [
            'password' => null,
            'prefix'   => '',
            'driver'   => null
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'adodb':
                $db = Mode::adodb($config['file'], $config['password'], $config['driver']);
                break;
            case 'odbc':
                $db = Mode::odbc($config['file'], $config['password'], $config['driver']);
                break;
            case 'pdo':
                $db = Mode::pdo($config['file'], $config['password'], $config['driver']);
                break;
            default:
                throw new DatabaseException("error db mode: {$mode}");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}
