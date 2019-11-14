<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\middleware\driver\odbc;

use fize\db\exception\DriverException;

/**
 * ODBC预处理语句
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
     * 对结果集设置处理二进制数据的模式
     * @param int $mode 指定模式， 可选值 ODBC_BINMODE_PASSTHRU | ODBC_BINMODE_RETURN | ODBC_BINMODE_CONVERT
     * @return bool
     * @throws DriverException
     */
    public function binmode($mode)
    {
        $rst = odbc_binmode($this->statement, $mode);
        if($rst === false){
            throw new DriverException(odbc_error(), odbc_errormsg());
        }
        return $rst;
    }

    /**
     * 返回结果游标名称
     * @return string
     */
    public function cursor()
    {
        return odbc_cursor($this->statement);
    }

    /**
     * 执行当前预处理语句
     * @param array $parameters_array 可选的参数
     * @throws DriverException
     */
    public function execute(array $parameters_array = [])
    {
        $rst = odbc_execute($this->statement, $parameters_array);
        if($rst === false){
            throw new DriverException(odbc_error(), odbc_errormsg());
        }
    }

    /**
     * 以数组形式遍历结果集
     * @param int $rownumber 指定要检索的行数
     * @return array
     */
    public function fetchArray($rownumber = null)
    {
        return odbc_fetch_array($this->statement, $rownumber);
    }

    /**
     * 遍历结果集到指定数组
     * @param array $result_array 结果集将添加到该数组
     * @param int $rownumber 指定要检索的行数
     * @return int 返回结果行数
     * @throws DriverException
     */
    public function fetchInto(array &$result_array, $rownumber = null)
    {
        $rst = odbc_fetch_into($this->statement, $result_array, $rownumber);
        if($rst === false){
            throw new DriverException(odbc_error(), odbc_errormsg());
        }
        return $rst;
    }

    /**
     * 以对象形式遍历结果集
     * @param int $rownumber 指定要检索的行数
     * @return object 一个对象表示一个行
     */
    public function fetchObject($rownumber = null)
    {
        return odbc_fetch_object($this->statement, $rownumber);
    }

    /**
     * 移动结果集指针，使用该方法后，下次使用odbc_result()将返回下一行的结果
     * @param int $row_number 指定要检索的行数
     * @return bool 成功返回true，失败返回false
     */
    public function fetchRow($row_number = null)
    {
        return odbc_fetch_row($this->statement, $row_number);
    }

    /**
     * 获取字段的长度(精度)
     * @param int $field_number 字段下标(从1开始)
     * @return false|int 失败时返回false
     */
    public function fieldLen($field_number)
    {
        return odbc_field_len($this->statement, $field_number);
    }

    /**
     * 获取字段名称
     * @param int $field_number 字段下标(从1开始)
     * @return false|string 失败时返回false
     */
    public function fieldName($field_number)
    {
        return odbc_field_name($this->statement, $field_number);
    }

    /**
     * 获取字段下标(从1开始)
     * @param string $field_name 字段名称
     * @return false|int 失败时返回false
     */
    public function fieldNum($field_name)
    {
        return odbc_field_num($this->statement, $field_name);
    }

    /**
     * 获取字段的长度(精度)
     * @notice 该方法是fieldLen的别名
     * @deprecated 别名方法，建议统一使用fieldLen方法
     * @param int $field_number 字段下标(从1开始)
     * @return false|int 失败时返回false
     */
    public function fieldPrecision($field_number)
    {
        return odbc_field_precision($this->statement, $field_number);
    }

    /**
     * 获取字段的小数位数
     * @param int $field_number 字段下标(从1开始)
     * @return false|int 失败时返回false
     */
    public function fieldScale($field_number)
    {
        return odbc_field_scale($this->statement, $field_number);
    }

    /**
     * 获取字段的类型
     * @param int $field_number 字段下标(从1开始)
     * @return false|string 失败时返回false
     */
    public function fieldType($field_number)
    {
        return odbc_field_type($this->statement, $field_number);
    }

    /**
     * 释放当前结果内存
     * @return bool
     */
    public function freeResult()
    {
        $result = odbc_free_result($this->statement);
        $this->statement = null;
        return $result;
    }

    /**
     * 设置允许的最长字段列处理
     * @param int $length 最长字段长度
     * @return bool
     */
    public function longreadlen($length)
    {
        return odbc_longreadlen($this->statement, $length);
    }

    /**
     * 对于多个结果集，将指针移到下个结果集
     * @return bool
     */
    public function nextResult()
    {
        return odbc_next_result($this->statement);
    }

    /**
     * 返回结果中的列数
     * @return int
     */
    public function numFields()
    {
        return odbc_num_fields($this->statement);
    }

    /**
     * 返回结果中的行(记录)数,对于操作，则返回受影响的行数
     * @return int
     */
    public function numRows()
    {
        return odbc_num_rows($this->statement);
    }

    /**
     * 将结果以HTML表格形式打印出来。
     * @param string $format 附加的整体表格格式。
     * @return int 返回结果集大小，失败返回false
     * @throws DriverException
     */
    public function resultAll($format = null)
    {
        $rst = odbc_result_all($this->statement, $format);
        if($rst === false){
            throw new DriverException(odbc_error(), odbc_errormsg());
        }
        return $rst;
    }

    /**
     * 获取当前结果记录的某列值
     * @param mixed $field 字段名或者字段顺序
     * @return mixed
     */
    public function result($field)
    {
        return odbc_result($this->statement, $field);
    }

    /**
     * 改变属性
     * @param int $option 属性名
     * @param int $param 属性值
     * @return bool
     */
    public function setoption($option, $param)
    {
        return odbc_setoption($this->statement, 2, $option, $param);
    }
}