<?php

namespace fize\database\extend\sqlite\mode;

use fize\database\extend\sqlite\Db;
use fize\database\middleware\Odbc as Middleware;

/**
 * ODBC
 *
 * ODBC方式Sqlite3数据库模型类
 */
class Odbc extends Db
{

    use Middleware;

    /**
     * 构造
     * @param string      $filename    数据库文件路径
     * @param int         $long_names  参数LongNames
     * @param int         $time_out    参数Timeout
     * @param int         $no_txn      参数NoTXN
     * @param string      $sync_pragma 参数SyncPragma
     * @param int         $step_api    参数StepAPI
     * @param string|null $driver      指定ODBC驱动
     */
    public function __construct(string $filename, int $long_names = 0, int $time_out = 1000, int $no_txn = 0, string $sync_pragma = "NORMAL", int $step_api = 0, string $driver = null)
    {
        if (is_null($driver)) {  //默认驱动名
            $driver = "SQLite3 ODBC Driver";
        }
        $dsn = "DRIVER=$driver;Database=$filename;LongNames=$long_names;Timeout=$time_out;NoTXN=$no_txn;SyncPragma=$sync_pragma;StepAPI=$step_api";
        $this->odbcConstruct($dsn, '', '');
    }

    /**
     * 析构时关闭ODBC
     */
    public function __destruct()
    {
        $this->odbcDestruct();
        parent::__destruct();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在sqlite3中无效
     * @return int|string
     */
    public function lastInsertId(string $name = null)
    {
        $result = $this->driver->exec("SELECT LAST_INSERT_ROWID()");
        return $result->result(1);
    }
}
