<?php

namespace fize\db\realization\sqlite\mode;


use fize\db\realization\sqlite\Db;
use fize\db\middleware\Odbc as Middleware;

/**
 * ODBC方式Sqlite3数据库模型类
 * 注意ODBC返回的类型都为字符串格式(null除外)
 */
class Odbc extends Db
{

    use Middleware {
        Middleware::query as protected queryOdbc;
    }

    /**
     * 构造
     * @param $filename
     * @param string $prefix
     * @param int $long_names
     * @param int $time_out
     * @param int $no_txn
     * @param string $sync_pragma
     * @param int $step_api
     * @param string $driver
     */
    public function __construct($filename, $prefix = "", $long_names = 0, $time_out = 1000, $no_txn = 0, $sync_pragma = "NORMAL", $step_api = 0, $driver = null)
    {
        $this->tablePrefix = $prefix;
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

    /**
     * 安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * ODBC为驱动层，安全化值应由各数据库自行实现
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . addcslashes($value, "'") . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $result = $this->queryOdbc($sql, $params, $callback);
        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
            $this->driver->exec("SELECT LAST_INSERT_ROWID()");
            return $this->driver->result(1);  //返回自增ID
        } elseif (stripos($sql, "SELECT") === 0) {
            return $result;
        } else {
            $this->driver->exec("SELECT CHANGES()");
            $rows = $this->driver->result(1);
            return (int)$rows; //返回受影响条数
        }
    }
}