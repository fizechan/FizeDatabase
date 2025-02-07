<?php

namespace Fize\Database\Extend\PgSQL\Mode;

use Fize\Database\Extend\PgSQL\Db;
use Fize\Database\Extend\PgSQL\Driver\PgSQL as Driver;
use Fize\Exception\DatabaseException;

/**
 * Pgsql
 *
 * PGSQL原生方式PostgreSQL数据库模型类
 */
class PgSQL extends Db
{

    /**
     * @var Driver 使用的Pgsql驱动对象
     */
    protected $driver = null;

    /**
     * 构造
     * @param string   $connection_string 连接字符串
     * @param bool     $pconnect          是否使用长连接
     * @param int|null $connect_type      PGSQL_CONNECT_FORCE_NEW使用新连接
     */
    public function __construct(string $connection_string, bool $pconnect = false, int $connect_type = null)
    {
        $this->driver = new Driver($connection_string, $pconnect, $connect_type);
    }

    /**
     * 析构时释放连接
     */
    public function __destruct()
    {
        $this->driver = null;
        parent::__destruct();
    }

    /**
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，原$*占位符统一变更为?占位符
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array 返回结果数组
     */
    public function query(string $sql, array $params = [], callable $callback = null): array
    {
        if ($params) {  //将?占位符还原为$*占位符
            $parts = explode('?', $sql);
            $temp_sql = $parts[0];
            for ($i = 1; $i < count($parts); $i++) {
                $temp_sql .= "$" . $i . $parts[$i];
            }
            $sql = $temp_sql;
        }
        $result = $this->driver->queryParams($sql, $params);

        if ($result === false) {
            throw new DatabaseException($this->driver->lastError());
        }

        if ($callback !== null) {
            $rows = [];
            while ($row = $result->fetchAssoc()) {
                $callback($row);
                $rows[] = $row;
            }
            $result->freeResult();
        } else {
            $rows = $result->fetchAll(PGSQL_ASSOC);
        }
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
        if ($params) {  //将?占位符还原为$*占位符
            $parts = explode('?', $sql);
            $temp_sql = $parts[0];
            for ($i = 1; $i < count($parts); $i++) {
                $temp_sql .= "$" . $i . $parts[$i];
            }
            $sql = $temp_sql;
        }
        $result = $this->driver->queryParams($sql, $params);

        if ($result === false) {
            throw new DatabaseException($this->driver->lastError());
        }

        return $result->affectedRows();
    }

    /**
     * 开始事务
     */
    public function startTrans()
    {
        $this->driver->query("BEGIN");
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->driver->query("COMMIT");
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->driver->query("ROLLBACK");
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在PostgreSQL中必须指定
     * @return int|string
     */
    public function lastInsertId(string $name = null)
    {
        $sql = "SELECT currval('$name')";
        $result = $this->driver->query($sql);
        $row = $result->fetchRow();
        return $row[0];
    }
}
