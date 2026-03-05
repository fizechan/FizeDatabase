<?php

namespace Fize\Database\Extend\SQLite;

use Fize\Database\Extend\SQLite\Mode\ODBCMode;
use Fize\Database\Extend\SQLite\Mode\PDOMode;
use Fize\Database\Extend\SQLite\Mode\SQLite3Mode;

/**
 * 模式
 *
 * Sqlite的ORM模型
 */
class Mode
{

    /**
     * odbc方式构造
     * @param string      $filename    数据库文件路径
     * @param int         $long_names  参数LongNames
     * @param int         $time_out    参数Timeout
     * @param int         $no_txn      参数NoTXN
     * @param string      $sync_pragma 参数SyncPragma
     * @param int         $step_api    参数StepAPI
     * @param string|null $driver      指定ODBC驱动
     * @return ODBCMode
     */
    public static function odbc(string $filename, int $long_names = 0, int $time_out = 1000, int $no_txn = 0, string $sync_pragma = "NORMAL", int $step_api = 0, string $driver = null): ODBCMode
    {
        return new ODBCMode($filename, $long_names, $time_out, $no_txn, $sync_pragma, $step_api, $driver);
    }

    /**
     * pdo方式构造
     * @param string $filename 数据库文件路径
     * @return PDOMode
     */
    public static function pdo(string $filename): PDOMode
    {
        return new PDOMode($filename);
    }

    /**
     * sqlite3构造
     * @param string      $filename       数据库文件路径
     * @param int         $flags          模式，默认是SQLITE3_OPEN_READWRITE
     * @param string|null $encryption_key 加密密钥
     * @param int         $busy_timeout   超时时间
     * @return SQLite3Mode
     */
    public static function sqlite3(string $filename, int $flags = 2, string $encryption_key = null, int $busy_timeout = 30000): SQLite3Mode
    {
        return new SQLite3Mode($filename, $flags, $encryption_key, $busy_timeout);
    }
}
