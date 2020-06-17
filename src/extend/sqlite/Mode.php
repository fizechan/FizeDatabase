<?php

namespace fize\database\extend\sqlite;

use fize\database\extend\sqlite\mode\Odbc;
use fize\database\extend\sqlite\mode\Sqlite3;
use fize\database\extend\sqlite\mode\Pdo;

/**
 * 模式
 *
 * Sqlite的ORM模型
 */
class Mode
{

    /**
     * odbc方式构造
     * @param string $filename    数据库文件路径
     * @param int    $long_names  参数LongNames
     * @param int    $time_out    参数Timeout
     * @param int    $no_txn      参数NoTXN
     * @param string $sync_pragma 参数SyncPragma
     * @param int    $step_api    参数StepAPI
     * @param string $driver      指定ODBC驱动
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
     * @param string $filename       数据库文件路径
     * @param int    $flags          模式，默认是SQLITE3_OPEN_READWRITE
     * @param string $encryption_key 加密密钥
     * @param int    $busy_timeout   超时时间
     * @return Sqlite3
     */
    public static function sqlite3($filename, $flags = 2, $encryption_key = null, $busy_timeout = 30000)
    {
        return new Sqlite3($filename, $flags, $encryption_key, $busy_timeout);
    }
}
