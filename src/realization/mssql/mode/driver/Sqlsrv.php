<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\realization\mssql\mode\driver;


use Exception;


/**
 * MSSQL的官方支持方法对应类
 */
class Sqlsrv
{

    /**
     * 当前数据库链接对象
     * @var resource
     */
    private $conn = null;

    /**
     * 当前预处理对象
     * @var resource
     */
    private $stmt = null;

    /**
     * MSSQL constructor.
     * @param string $serverName 服务器,它可以包含一个端口号，则写法如{name},{port}
     * @param array $connectionInfo 其他信息
     * @throws Exception
     */
    public function __construct($serverName, array $connectionInfo = null)
    {
        $this->conn = self::connect($serverName, $connectionInfo);
        if (!$this->conn) {
            $error = end(self::errors(SQLSRV_ERR_ERRORS));
            throw new Exception($error['message'], $error['code']);
        }
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        if ($this->stmt && is_resource($this->stmt) && get_resource_type($this->stmt) == "SQL Server Statement") {
            $this->freeStmt();
        }
        if ($this->conn && is_resource($this->conn) && get_resource_type($this->conn) == "SQL Server Connection") {
            $this->close();
        }
    }

    /**
     * 开始一个事务
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function beginTransaction()
    {
        return sqlsrv_begin_transaction($this->conn);
    }

    /**
     * 取消预处理对象，但其可以再次使用execute方法运行
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE，如果当前没有预处理对象也返回false。
     */
    public function cancel()
    {
        if (is_null($this->stmt)) {
            return false;
        }
        return sqlsrv_cancel($this->stmt);
    }

    /**
     * 返回关于客户端和指定连接的信息。
     * @return array
     */
    public function clientInfo()
    {
        return sqlsrv_client_info($this->conn);
    }

    /**
     * 关闭当前数据库连接
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function close()
    {
        return sqlsrv_close($this->conn);
    }

    /**
     * 提交当前事务
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function commit()
    {
        return sqlsrv_commit($this->conn);
    }

    /**
     * 更改驱动程序错误处理和日志配置。
     * @param string $setting 设置名。可能的值："WarningsReturnAsErrors", "LogSubsystems", and "LogSeverity"。
     * @param mixed $value 设置值。可能的值参照PHP手册
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public static function configure($setting, $value)
    {
        return sqlsrv_configure($setting, $value);
    }

    /**
     * 创建一个数据库连接
     * @param string $serverName 服务器名
     * @param array $connectionInfo 设置项
     * @return resource
     */
    public static function connect($serverName, array $connectionInfo = null)
    {
        return sqlsrv_connect($serverName, $connectionInfo);
    }

    /**
     * 获取数据库连接的所有错误，
     * @param int $errorsOrWarnings 可选值：SQLSRV_ERR_ALL, SQLSRV_ERR_ERRORS, SQLSRV_ERR_WARNINGS.
     * @return array 键名包括SQLSTATE、code、message，没有错误时返回null
     */
    public static function errors($errorsOrWarnings = null)
    {
        return sqlsrv_errors($errorsOrWarnings);
    }

    /**
     * 执行当前预处理对象。
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function execute()
    {
        return sqlsrv_execute($this->stmt);
    }

    /**
     * 以数组形式遍历记录集
     * @param callable $func 遍历函数
     * @param int $fetchType 指定遍历类型
     * @param int $row 设置游标类型
     * @param int $offset 设置偏移量
     */
    public function fetchArray($func, $fetchType = 2, $row = null, $offset = 0)
    {
        if (is_null($row)) {
            while ($row = sqlsrv_fetch_array($this->stmt, $fetchType)) {
                $func($row);
            }
        } else {
            while ($row = sqlsrv_fetch_array($this->stmt, $fetchType, $row, $offset)) {
                $func($row);
            }
        }
    }

    /**
     * 以对象形式遍历记录集
     * @todo 只实现了简单的参数带入
     * @param callable $func 遍历函数
     * @param string $className 指定要生成实例的对象名，如果不指定，则生成其自身对象实例
     * @param array $ctorParams 如果对象实例化需要参数，则在此填写
     * @param int $row 设置游标类型
     * @param int $offset 设置偏移量
     */
    public function fetchObject($func, $className = null, array $ctorParams = null, $row = 6, $offset = null)
    {
        while($obj = sqlsrv_fetch_object($this->stmt, $className, $ctorParams, $row, $offset)){
        //while ($obj = sqlsrv_fetch_object($this->_stmt)) {
            $func($obj);
        }
    }

    /**
     * 执行该行数后指针指向下一个记录行
     * @param int $row 设置游标类型
     * @param int $offset 设置偏移量
     * @return mixed 成功返回true，失败返回false，没有更多记录时返回null
     */
    public function fetch($row = null, $offset = null)
    {
        if (is_null($row)) {
            return sqlsrv_fetch($this->stmt);
        } else {
            return sqlsrv_fetch($this->stmt, $row, $offset);
        }
    }

    /**
     * 检索准备好的语句字段的元数据。
     * @return array 失败是返回false
     */
    public function fieldMetadata()
    {
        return sqlsrv_field_metadata($this->stmt);
    }

    /**
     * 释放当前预处理语句的所有资源
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function freeStmt()
    {
        $result = sqlsrv_free_stmt($this->stmt);
        $this->stmt = null;
        return $result;
    }

    /**
     * 返回指定配置设置的值。
     * @param string $setting 设置名
     * @return mixed
     */
    public static function getConfig($setting)
    {
        return sqlsrv_get_config($setting);
    }

    /**
     * 获取当前行的指定字段值
     * @param int $fieldIndex 字段下标，以0开始。
     * @param int $getAsType 指定类型。
     * @return mixed
     */
    public function getField($fieldIndex, $getAsType = null)
    {
        if (is_null($getAsType)) {
            return sqlsrv_get_field($this->stmt, $fieldIndex);
        } else {
            return sqlsrv_get_field($this->stmt, $fieldIndex, $getAsType);
        }
    }

    /**
     * 判断当前预处理结果是否有记录
     * @return bool
     */
    public function hasRows()
    {
        return sqlsrv_has_rows($this->stmt);
    }

    /**
     * 将指针移动到下个记录集
     * @return mixed 成功返回true，失败返回false，没有更多记录集时返回null
     */
    public function nextResult()
    {
        return sqlsrv_next_result($this->stmt);
    }

    /**
     * 获取当前记录集的字段个数
     * @return int 如果失败返回false
     */
    public function numFields()
    {
        return sqlsrv_num_fields($this->stmt);
    }

    /**
     * 获取当前记录集的记录个数
     * @return int 如果失败返回false
     */
    public function numRows()
    {
        return sqlsrv_num_rows($this->stmt);
    }

    /**
     * 设置一个预处理语句
     * @param string $sql 预处理SQL语句，支持问号占位符。
     * @param array $params 可选的绑定参数
     * @param array $options 其他相关参数。
     * @return resource 失败是返回false
     */
    public function prepare($sql, array $params = null, array $options = null)
    {
        if (is_null($options)) {
            $this->stmt = sqlsrv_prepare($this->conn, $sql, $params);
        } else {
            $this->stmt = sqlsrv_prepare($this->conn, $sql, $params, $options);
        }
        return $this->stmt;
    }

    /**
     * 设置一个预处理语句，并执行。
     * @param string $sql 预处理SQL语句，支持问号占位符。
     * @param array $params 可选的绑定参数
     * @param array $options 其他相关参数。
     * @return resource 失败是返回false
     */
    public function query($sql, array $params = null, array $options = null)
    {
        if (is_null($options)) {
            $this->stmt = sqlsrv_query($this->conn, $sql, $params);
        } else {
            $this->stmt = sqlsrv_query($this->conn, $sql, $params, $options);
        }
        return $this->stmt;
    }

    /**
     * 事务回滚
     * @return bool 成功返回true，失败返回false。
     */
    public function rollback()
    {
        return sqlsrv_rollback($this->conn);
    }

    /**
     * 返回当前预处理语句的影响行数。
     * @return int
     */
    public function rowsAffected()
    {
        return sqlsrv_rows_affected($this->stmt);
    }

    /**
     * 如果绑定参数中含有流式数据，需要以此方法发送数据到数据库服务器。
     * @return bool 成功返回true，失败返回false。
     */
    public function sendStreamData()
    {
        return sqlsrv_send_stream_data($this->stmt);
    }

    /**
     * 获取数据库服务器信息。
     * @return array
     */
    public function serverInfo()
    {
        return sqlsrv_server_info($this->conn);
    }
}