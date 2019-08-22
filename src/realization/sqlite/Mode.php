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
     * @todo ODBC本身未实现数据库特性，仅适用于一般性调用
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
     * @todo PDO是未来趋势，建议谨慎使用sqlite3方式
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
     * pdo大法好
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
     * @param array $options 数据库参数选项
     * @return Db
     * @throws DbException
     */
    public static function getInstance(array $options)
    {
        $option = $options['option'];
        $db = null;
        switch ($options['mode']) {
            case 'odbc':
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $long_names = isset($option['long_names']) ? $option['long_names'] : 0;
                $time_out = isset($option['time_out']) ? $option['time_out'] : 1000;
                $no_txn = isset($option['no_txn']) ? $option['no_txn'] : 0;
                $sync_pragma = isset($option['sync_pragma']) ? $option['sync_pragma'] : 'NORMAL';
                $step_api = isset($option['step_api']) ? $option['step_api'] : 0;
                $driver = isset($option['driver']) ? $option['driver'] : null;
                $db = self::odbc($option['db_file'], $prefix, $long_names, $time_out, $no_txn, $sync_pragma, $step_api, $driver);
                break;
            case 'sqlite3':
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $flags = isset($option['flags']) ? $option['flags'] : 2;
                $encryption_key = isset($option['encryption_key']) ? $option['encryption_key'] : null;
                $busy_timeout = isset($option['busy_timeout']) ? $option['busy_timeout'] : 30000;
                $db = self::sqlite3($option['db_file'], $prefix, $flags, $encryption_key, $busy_timeout);
                break;
            case 'pdo':
                $prefix = isset($option['prefix']) ? $option['prefix'] : '';
                $db = self::pdo($option['db_file'], $prefix);
                break;
            default:
                throw new DbException("error db mode: {$options['mode']}");
        }
        return $db;
    }
}