<?php

namespace fize\db\realization\sqlite\mode;


use fize\db\realization\sqlite\Db;
use SQLite3 as Driver;
use Exception;

/**
 * SQLite3数据库模型类
 * 驱动相关：php_sqlite3.dll
 */
class Sqlite3 extends Db
{

    /**
     * 使用的SQLite3对象
     * @var Driver
     */
    private $_driver = null;

    /**
     * 构造
     * @param string $filename 数据库文件路径
     * @param string $prefix 表前缀
     * @param int $flags 模式，默认是SQLITE3_OPEN_READWRITE
     * @param string $encryption_key 加密密钥
     * @param int $busy_timeout
     */
    public function __construct($filename, $prefix = "", $flags = 2, $encryption_key = null, $busy_timeout = 30000)
    {
        $this->_tablePrefix = $prefix;
        $this->_driver = new Driver($filename, $flags, $encryption_key);
        $this->_driver->busyTimeout($busy_timeout);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->_driver->close();
    }

    /**
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return Driver
     */
    public function prototype()
    {
        return $this->_driver;
    }

    /**
     * SQLite3实现的安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . Driver::escapeString($value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
     * @throws Exception
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $stmt = $this->_driver->prepare($sql);

        if(!$stmt){
            throw new Exception($this->_driver->lastErrorMsg(), $this->_driver->lastErrorCode());
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

        if($result === false){
            throw new Exception($this->_driver->lastErrorMsg(), $this->_driver->lastErrorCode());
        }

        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
            $id = $this->_driver->lastInsertRowID();
            $stmt->close();
            return $id; //返回自增ID
        } elseif (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($assoc = $result->fetchArray(SQLITE3_ASSOC)) {
                    $callback($assoc);
                }
                $stmt->close();
                return null;
            } else {
                $out = [];
                while ($assoc = $result->fetchArray(SQLITE3_ASSOC)) {
                    $out[] = $assoc;
                }
                $stmt->close();
                return $out; //返回数组
            }
        } else {
            $rows = $this->_driver->changes();
            $stmt->close();
            return $rows; //返回受影响条数
        }
    }

    /**
     * 开始事务
     * @return void
     */
    public function startTrans()
    {
        $this->_driver->query('BEGIN TRANSACTION');
    }

    /**
     * 执行事务
     * @return void
     */
    public function commit()
    {
        $this->_driver->query('COMMIT');
    }

    /**
     * 回滚事务
     * @return void
     */
    public function rollback()
    {
        $this->_driver->query('ROLLBACK');
    }
}