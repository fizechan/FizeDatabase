<?php

namespace Fize\Database\Extend\SQLite\Mode;

use Exception;
use Fize\Database\Extend\SQLite\Db;
use SQLite3 as SysSQLite3;

/**
 * Sqlite3
 *
 * SQLite3数据库模型类
 */
class SQLite3 extends Db
{

    /**
     * @var SysSQLite3 使用的SQLite3对象
     */
    private $driver;

    /**
     * 构造
     * @param string      $filename       数据库文件路径
     * @param int         $flags          模式，默认是SQLITE3_OPEN_READWRITE
     * @param string|null $encryption_key 加密密钥
     * @param int         $busy_timeout   超时时间
     */
    public function __construct(string $filename, int $flags = 2, string $encryption_key = null, int $busy_timeout = 30000)
    {
        $this->driver = new SysSQLite3($filename, $flags, $encryption_key);
        $this->driver->busyTimeout($busy_timeout);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->driver->close();
    }

    /**
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持原生的问号预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array 返回结果数组
     */
    public function query(string $sql, array $params = [], callable $callback = null): array
    {
        $stmt = $this->driver->prepare($sql);
        if (!$stmt) {
            throw new Exception($this->driver->lastErrorMsg(), $this->driver->lastErrorCode());
        }

        if (!empty($params)) {
            foreach ($params as $key => $val) {
                //类型判断
                if (is_integer($val)) {
                    $vtype = SQLITE3_INTEGER;
                } elseif (is_double($val)) {
                    $vtype = SQLITE3_FLOAT;
                } elseif (is_object($val) || is_resource($val)) {
                    $vtype = SQLITE3_BLOB;
                } elseif (is_null($val)) {
                    $vtype = SQLITE3_NULL;
                } else {
                    $vtype = SQLITE3_TEXT;
                }
                $stmt->bindValue($key + 1, $val, $vtype); //位置是从1开始而不是下标0,使用bindValue直接绑定值，而不是使用bindParam绑定引用
            }
        }
        $result = $stmt->execute();

        if ($result === false) {
            throw new Exception($this->driver->lastErrorMsg(), $this->driver->lastErrorCode());
        }

        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if ($callback !== null) {
                $callback($row);
            }
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    /**
     * 执行一个SQL语句
     * @param string $sql    SQL语句，支持问号预处理语句
     * @param array  $params 可选的绑定参数
     * @return int 返回受影响行数
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->driver->prepare($sql);
        if (!$stmt) {
            throw new Exception($this->driver->lastErrorMsg(), $this->driver->lastErrorCode());
        }

        if (!empty($params)) {
            foreach ($params as $key => $val) {
                //类型判断
                if (is_integer($val)) {
                    $vtype = SQLITE3_INTEGER;
                } elseif (is_double($val)) {
                    $vtype = SQLITE3_FLOAT;
                } elseif (is_object($val) || is_resource($val)) {
                    $vtype = SQLITE3_BLOB;
                } elseif (is_null($val)) {
                    $vtype = SQLITE3_NULL;
                } else {
                    $vtype = SQLITE3_TEXT;
                }
                $stmt->bindValue($key + 1, $val, $vtype); //位置是从1开始而不是下标0,使用bindValue直接绑定值，而不是使用bindParam绑定引用
            }
        }
        $result = $stmt->execute();

        if ($result === false) {
            throw new Exception($this->driver->lastErrorMsg(), $this->driver->lastErrorCode());
        }

        $rows = $this->driver->changes();
        $stmt->close();
        return $rows;
    }

    /**
     * 开始事务
     * @return void
     */
    public function startTrans()
    {
        $this->driver->query('BEGIN TRANSACTION');
    }

    /**
     * 执行事务
     * @return void
     */
    public function commit()
    {
        $this->driver->query('COMMIT');
    }

    /**
     * 回滚事务
     * @return void
     */
    public function rollback()
    {
        $this->driver->query('ROLLBACK');
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在sqlite3中无效
     * @return int
     */
    public function lastInsertId(string $name = null)
    {
        return $this->driver->lastInsertRowID();
    }
}
