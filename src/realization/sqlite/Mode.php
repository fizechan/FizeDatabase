<?php

namespace fize\db\realization\sqlite;


use fize\db\definition\Mode as ModeInterface;
use fize\db\realization\sqlite\mode\Odbc;
use fize\db\realization\sqlite\mode\Sqlite3;
use fize\db\realization\sqlite\mode\Pdo;
use fize\db\exception\Exception;


/**
 * 模式
 *
 * Sqlite的ORM模型
 */
class Mode implements ModeInterface
{

    /**
     * odbc方式构造
     * @param string $filename 数据库文件路径
     * @param int $long_names 参数LongNames
     * @param int $time_out 参数Timeout
     * @param int $no_txn 参数NoTXN
     * @param string $sync_pragma 参数SyncPragma
     * @param int $step_api 参数StepAPI
     * @param string $driver 指定ODBC驱动
     * @return Odbc
     */
    public static function odbc($filename, $long_names = 0, $time_out = 1000, $no_txn = 0, $sync_pragma = "NORMAL", $step_api = 0, $driver = null)
    {
        return new Odbc($filename, $long_names, $time_out, $no_txn, $sync_pragma, $step_api, $driver);
    }

    /**
     * pdo方式构造
     * @param string $filename 数据库文件路径
     * @return Pdo
     */
    public static function pdo($filename)
    {
        return new Pdo($filename);
    }

    /**
     * sqlite3构造
     * @param string $filename 数据库文件路径
     * @param int $flags 模式，默认是SQLITE3_OPEN_READWRITE
     * @param string $encryption_key 加密密钥
     * @param int $busy_timeout 超时时间
     * @return Sqlite3
     */
    public static function sqlite3($filename, $flags = 2, $encryption_key = null, $busy_timeout = 30000)
    {
        return new Sqlite3($filename, $flags, $encryption_key, $busy_timeout);
    }

    /**
     * 数据库实例
     * @param array $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function getInstance(array $config)
    {
        $mode = isset($config['mode']) ? $config['mode'] : 'pdo';
        $dbcfg = $config['config'];
        $default_dbcfg = [
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
        $dbcfg = array_merge($default_dbcfg, $dbcfg);
        switch ($mode) {
            case 'odbc':
                $db = self::odbc($dbcfg['file'], $dbcfg['long_names'], $dbcfg['time_out'], $dbcfg['no_txn'], $dbcfg['sync_pragma'], $dbcfg['step_api'], $dbcfg['driver']);
                break;
            case 'sqlite3':
                $db = self::sqlite3($dbcfg['file'], $dbcfg['flags'], $dbcfg['encryption_key'], $dbcfg['busy_timeout']);
                break;
            case 'pdo':
                $db = self::pdo($dbcfg['file']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($dbcfg['prefix']);
        return $db;
    }
}