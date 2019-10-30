<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\realization\mssql\mode\driver\sqlsrv;

/**
 * Sqlsrv预处理语句
 * @package fize\db\realization\mssql\mode\driver\sqlsrv
 */
class Statement
{

    /**
     * @var resource 预处理语句
     */
    protected $statement;

    /**
     * 构造
     * @param resource $statement 预处理语句资源对象
     */
    public function __construct(&$statement)
    {
        $this->statement = $statement;
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        if ($this->statement && is_resource($this->statement) && get_resource_type($this->statement) == "SQL Server Statement") {
            $this->freeStmt();
        }
    }

    /**
     * 取消预处理对象，但其可以再次使用execute方法运行
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE，如果当前没有预处理对象也返回false。
     */
    public function cancel()
    {
        if (is_null($this->statement)) {
            return false;
        }
        return sqlsrv_cancel($this->statement);
    }

    /**
     * 执行当前预处理对象。
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function execute()
    {
        return sqlsrv_execute($this->statement);
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
            while ($row = sqlsrv_fetch_array($this->statement, $fetchType)) {
                $func($row);
            }
        } else {
            while ($row = sqlsrv_fetch_array($this->statement, $fetchType, $row, $offset)) {
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
        while($obj = sqlsrv_fetch_object($this->statement, $className, $ctorParams, $row, $offset)){
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
            return sqlsrv_fetch($this->statement);
        } else {
            return sqlsrv_fetch($this->statement, $row, $offset);
        }
    }

    /**
     * 检索准备好的语句字段的元数据。
     * @return array 失败是返回false
     */
    public function fieldMetadata()
    {
        return sqlsrv_field_metadata($this->statement);
    }

    /**
     * 释放当前预处理语句的所有资源
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function freeStmt()
    {
        $result = sqlsrv_free_stmt($this->statement);
        $this->statement = null;
        return $result;
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
            return sqlsrv_get_field($this->statement, $fieldIndex);
        } else {
            return sqlsrv_get_field($this->statement, $fieldIndex, $getAsType);
        }
    }

    /**
     * 判断当前预处理结果是否有记录
     * @return bool
     */
    public function hasRows()
    {
        return sqlsrv_has_rows($this->statement);
    }

    /**
     * 将指针移动到下个记录集
     * @return mixed 成功返回true，失败返回false，没有更多记录集时返回null
     */
    public function nextResult()
    {
        return sqlsrv_next_result($this->statement);
    }

    /**
     * 获取当前记录集的字段个数
     * @return int 如果失败返回false
     */
    public function numFields()
    {
        return sqlsrv_num_fields($this->statement);
    }

    /**
     * 获取当前记录集的记录个数
     * @return int 如果失败返回false
     */
    public function numRows()
    {
        return sqlsrv_num_rows($this->statement);
    }

    /**
     * 返回当前预处理语句的影响行数。
     * @return int
     */
    public function rowsAffected()
    {
        return sqlsrv_rows_affected($this->statement);
    }

    /**
     * 如果绑定参数中含有流式数据，需要以此方法发送数据到数据库服务器。
     * @return bool 成功返回true，失败返回false。
     */
    public function sendStreamData()
    {
        return sqlsrv_send_stream_data($this->statement);
    }
}