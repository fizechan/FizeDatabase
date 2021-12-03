<?php


namespace Fize\Database\Extend\MSSQL;


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
        $mode = $mode ?: 'pdo';
        $default_config = [
            'port'        => '',
            'prefix'      => '',
            'new_feature' => true,
            'driver'      => null,
            'charset'     => 'GBK',
            'opts'        => []
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'adodb':
                $db = Mode::adodb($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['driver']);
                break;
            case 'odbc':
                $db = Mode::odbc($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['driver']);
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
            case 'sqlsrv':
                $db = Mode::sqlsrv($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['charset']);
                break;
            default:
                throw new DatabaseException("error db mode: {$mode}");
        }
        $db->prefix($config['prefix']);
        $db->newFeature($config['new_feature']);
        return $db;
    }
}
