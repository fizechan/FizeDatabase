<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\realization\pgsql\mode;

use fize\db\realization\pgsql\Db;
use fize\db\realization\pgsql\mode\driver\Pgsql as Driver;
use fize\db\exception\DbException;

/**
 * PGSQL原生方式PostgreSQL数据库模型类
 * @package fize\db\realization\pgsql\mode
 */
class Pgsql extends Db
{

    /**
     * 使用的Pgsql驱动对象
     * @var Driver
     */
    protected $driver = null;

    /**
     * 构造
     * @param string $connection_string 连接字符串
     * @param bool $pconnect 是否使用长连接
     * @param int $connect_type PGSQL_CONNECT_FORCE_NEW使用新连接
     */
    public function __construct($connection_string, $pconnect = false, $connect_type = null)
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
     * 返回操作对象
     * @return Driver
     */
    public function prototype()
    {
        return $this->driver;
    }

    /**
     * pgsql函数实现的安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . $this->driver->escapeString($value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int|null SELECT语句返回数组，INSERT/REPLACE返回自增ID，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $result = $this->driver->queryParams($sql, $params);

        if ($result === false) {
            throw new DbException($this->driver->lastError());
        }

        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
            return 0;
        } elseif (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($row = $result->fetchAssoc()) {
                    $callback($row);
                }
                $result->freeResult();
                return null;
            } else {
                return $result->fetchAll(PGSQL_ASSOC);
            }
        } else {
            return $result->affectedRows();
        }
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
}
