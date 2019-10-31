<?php

namespace fize\db\realization\sqlite\mode;


use fize\db\realization\sqlite\Db;
use fize\db\middleware\Odbc as Middleware;

/**
 * ODBC方式Sqlite3数据库模型类
 */
class Odbc extends Db
{

    use Middleware;

    /**
     * 构造
     * @param string $filename 数据库文件路径
     * @param int $long_names 参数LongNames
     * @param int $time_out 参数Timeout
     * @param int $no_txn 参数NoTXN
     * @param string $sync_pragma 参数SyncPragma
     * @param int $step_api 参数StepAPI
     * @param string $driver 指定ODBC驱动
     */
    public function __construct($filename, $long_names = 0, $time_out = 1000, $no_txn = 0, $sync_pragma = "NORMAL", $step_api = 0, $driver = null)
    {
        if (is_null($driver)) {  //默认驱动名
            $driver = "SQLite3 ODBC Driver";
        }
        $dsn = "DRIVER={$driver};Database={$filename};LongNames={$long_names};Timeout={$time_out};NoTXN={$no_txn};SyncPragma={$sync_pragma};StepAPI={$step_api}";
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

//    /**
//     * 执行一个SQL语句并返回相应结果
//     * @param string $sql SQL语句，支持原生的ODBC问号预处理
//     * @param array $params 可选的绑定参数
//     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
//     * @return mixed SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
//     */
//    public function query2($sql, array $params = [], callable $callback = null)
//    {
//        $result = $this->queryOdbc($sql, $params, $callback);
//        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
//            $this->driver->exec("SELECT LAST_INSERT_ROWID()");
//            return $this->driver->result(1);  //返回自增ID
//        } elseif (stripos($sql, "SELECT") === 0) {
//            return $result;
//        } else {
//            $this->driver->exec("SELECT CHANGES()");
//            $rows = $this->driver->result(1);
//            return (int)$rows; //返回受影响条数
//        }
//    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在sqlite3中无效
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $result = $this->driver->exec("SELECT LAST_INSERT_ROWID()");
        return $result->result(1);
    }
}