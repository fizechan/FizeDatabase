<?php

namespace fize\db\realization\sqlite;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\sqlite\mode\Odbc;
use fize\db\realization\sqlite\mode\Sqlite3;
use fize\db\realization\sqlite\mode\Pdo;
use fize\db\exception\DbException;


/**
 * Sqlite的ORM模型
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @notice ODBC本身未实现数据库特性，仅适用于一般性调用
     * @param string $filename
     * @param string $prefix
     * @param int $long_names
     * @param int $time_out
     * @param int $no_txn
     * @param string $sync_pragma
     * @param int $step_api
     * @param null $driver
     * @return Odbc
     */
    public static function odbc($filename, $prefix = "", $long_names = 0, $time_out = 1000, $no_txn = 0, $sync_pragma = "NORMAL", $step_api = 0, $driver = null)
    {
        return new Odbc($filename, $prefix, $long_names, $time_out, $no_txn, $sync_pragma, $step_api, $driver);
    }

    /**
     * sqlite3构造
     * @notice PDO是未来趋势，建议谨慎使用sqlite3方式
     * @param string $filename 数据库文件路径
     * @param string $prefix 表前缀
     * @param int $flags 模式，默认是SQLITE3_OPEN_READWRITE
     * @param string $encryption_key 加密密钥
     * @param int $busy_timeout
     * @return Sqlite3
     */
    public static function sqlite3($filename, $prefix = "", $flags = 2, $encryption_key = null, $busy_timeout = 30000)
    {
        return new Sqlite3($filename, $prefix, $flags, $encryption_key, $busy_timeout);
    }

    /**
     * pdo方式构造
     * @param string $filename
     * @param string $prefix
     * @return Pdo
     */
    public static function pdo($filename, $prefix = "")
    {
        return new Pdo($filename, $prefix);
    }

    /**
     * 数据库实例
     * @param array $config 数据库参数选项
     * @return Db
     * @throws DbException
     */
    public static function getInstance(array $config)
    {
        $mode = isset($config['mode']) ? $config['mode'] : 'pdo';
        $db_cfg = $config['config'];
        $db = null;
        switch ($mode) {
            case 'odbc':
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $long_names = isset($db_cfg['long_names']) ? $db_cfg['long_names'] : 0;
                $time_out = isset($db_cfg['time_out']) ? $db_cfg['time_out'] : 1000;
                $no_txn = isset($db_cfg['no_txn']) ? $db_cfg['no_txn'] : 0;
                $sync_pragma = isset($db_cfg['sync_pragma']) ? $db_cfg['sync_pragma'] : 'NORMAL';
                $step_api = isset($db_cfg['step_api']) ? $db_cfg['step_api'] : 0;
                $driver = isset($db_cfg['driver']) ? $db_cfg['driver'] : null;
                $db = self::odbc($db_cfg['file'], $prefix, $long_names, $time_out, $no_txn, $sync_pragma, $step_api, $driver);
                break;
            case 'sqlite3':
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $flags = isset($db_cfg['flags']) ? $db_cfg['flags'] : 2;
                $encryption_key = isset($db_cfg['encryption_key']) ? $db_cfg['encryption_key'] : null;
                $busy_timeout = isset($db_cfg['busy_timeout']) ? $db_cfg['busy_timeout'] : 30000;
                $db = self::sqlite3($db_cfg['file'], $prefix, $flags, $encryption_key, $busy_timeout);
                break;
            case 'pdo':
                $prefix = isset($db_cfg['prefix']) ? $db_cfg['prefix'] : '';
                $db = self::pdo($db_cfg['file'], $prefix);
                break;
            default:
                throw new DbException("error db mode: {$mode}");
        }
        return $db;
    }
}