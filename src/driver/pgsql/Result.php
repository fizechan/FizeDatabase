<?php

namespace fize\database\driver\pgsql;

/**
 * PostgreSQL结果集
 */
class Result
{

    /**
     * @var resource 结果集
     */
    protected $result = null;

    /**
     * 构造
     * @param resource $result 结果集
     */
    public function __construct(&$result)
    {
        $this->result = $result;
    }

    /**
     * 析构时释放资源
     */
    public function __destruct()
    {
        $this->freeResult();
    }

    /**
     * 返回受影响的记录数目
     * @return int
     */
    public function affectedRows(): int
    {
        return pg_affected_rows($this->result);
    }

    /**
     * 以数组的形式获取特定结果列中的所有行
     * @param int $column 要从结果资源中检索的列号(从零开始)
     * @return array
     */
    public function fetchAllColumns(int $column = 0): array
    {
        return pg_fetch_all_columns($this->result, $column);
    }

    /**
     * 从结果中提取所有行作为一个数组
     * @param int $result_type 返回类型 PGSQL_ASSOC | PGSQL_NUM | PGSQL_BOTH
     * @return array
     */
    public function fetchAll(int $result_type = 1): array
    {
        return pg_fetch_all($this->result, $result_type);
    }

    /**
     * 提取一行作为数组
     * @param int|null $row         要从结果资源中检索的列号(从零开始)
     * @param int      $result_type 常量PGSQL_ASSOC，PGSQL_NUM 和 PGSQL_BOTH
     * @return array
     */
    public function fetchArray(int $row = null, int $result_type = 3): array
    {
        return pg_fetch_array($this->result, $row, $result_type);
    }

    /**
     * 提取一行作为关联数组
     * @param int|null $row 要从结果资源中检索的列号(从零开始)
     * @return array
     */
    public function fetchAssoc(int $row = null): array
    {
        return pg_fetch_assoc($this->result, $row);
    }

    /**
     * 提取一行作为对象
     * @param int|null $row 要从结果资源中检索的列号(从零开始)
     * @return object
     */
    public function fetchObject(int $row = null)
    {
        return pg_fetch_object($this->result, $row);
    }

    /**
     * 从结果资源中返回值
     * @param int   $row   要从结果资源中检索的列号(从零开始)
     * @param mixed $field 字段名（字符串）或字段索引（整数）。
     * @return mixed
     */
    public function fetchResult(int $row, $field)
    {
        return pg_fetch_result($this->result, $row, $field);
    }

    /**
     * 提取一行作为枚举数组
     * @param int|null $row 要从结果资源中检索的列号(从零开始),未指定则为下一行
     * @return array
     */
    public function fetchRow(int $row = null)
    {
        return pg_fetch_row($this->result, $row);
    }

    /**
     * 测试字段是否为 NULL
     * @param int   $row   要从结果资源中检索的列号(从零开始)
     * @param mixed $field 字段名（字符串）或字段索引（整数）。
     * @return int 为null返回1，不为null返回0
     */
    public function fieldIsNull(int $row, $field)
    {
        return pg_field_is_null($this->result, $row, $field);
    }

    /**
     * 返回字段的名字
     * @param int $field_number 字段编号从 0 开始
     * @return string
     */
    public function fieldName(int $field_number)
    {
        return pg_field_name($this->result, $field_number);
    }

    /**
     * 返回字段的编号
     * @param string $field_name 字段名
     * @return int 未找到或者出错时返回-1
     */
    public function fieldNum(string $field_name): int
    {
        return pg_field_num($this->result, $field_name);
    }

    /**
     * 返回打印出来的长度
     * @param int    $row_number 要从结果资源中检索的列号(从零开始)
     * @param string $field_name 字段名
     * @return int
     */
    public function fieldPrtlen(int $row_number, string $field_name): int
    {
        return pg_field_prtlen($this->result, $row_number, $field_name);
    }

    /**
     * 返回指定字段占用内部存储空间的大小
     * @param int $field_number 字段编号从 0 开始
     * @return int 字段大小为 -1 表示可变长度字段。如果出错本函数返回 FALSE
     */
    public function fieldSize(int $field_number): int
    {
        return pg_field_size($this->result, $field_number);
    }

    /**
     * 返回tables字段的名称或oid
     * @param int  $field_number 字段编号从 0 开始
     * @param bool $oid_only     默认情况下，返回字段所属的表名，但如果oid_only设置为TRUE，则返回oid。
     * @return mixed
     */
    public function fieldTable(int $field_number, bool $oid_only = false)
    {
        return pg_field_table($this->result, $field_number, $oid_only);
    }

    /**
     * 返回对应字段号的类型ID (OID)
     * @param int $field_number 字段编号从 0 开始
     * @return int
     */
    public function fieldTypeOid(int $field_number): int
    {
        return pg_field_type_oid($this->result, $field_number);
    }

    /**
     * 返回相应字段的类型名称
     * @param int $field_number 字段编号从 0 开始
     * @return string
     */
    public function fieldType(int $field_number): string
    {
        return pg_field_type($this->result, $field_number);
    }

    /**
     * 释放查询结果占用的内存
     */
    public function freeResult()
    {
        if ($this->result) {
            pg_free_result($this->result);
        }
        $this->result = null;
    }

    /**
     * 返回上一个对象的 oid
     * @notice 从 PostgreSQL 7.2 版开始 OID 字段成为可选项。故该方法可能返回false
     * @return string|false 没有定义oid时返回false
     */
    public function lastOid()
    {
        return pg_last_oid($this->result);
    }

    /**
     * 返回字段的数目
     * @return int
     */
    public function numFields(): int
    {
        return pg_num_fields($this->result);
    }

    /**
     * 返回行的数目
     * @return int
     */
    public function numRows(): int
    {
        return pg_num_rows($this->result);
    }

    /**
     * 返回错误报告的单个字段
     * @param int $fieldcode 指定常量配置
     * @return string
     */
    public function resultErrorField(int $fieldcode): string
    {
        return pg_result_error_field($this->result, $fieldcode);
    }

    /**
     * 获得查询结果的错误信息
     * @return string
     */
    public function resultError(): string
    {
        return pg_result_error($this->result);
    }

    /**
     * 在结果资源中设定内部行偏移量
     * @param int $offset 偏移量
     * @return bool
     */
    public function resultSeek(int $offset): bool
    {
        return pg_result_seek($this->result, $offset);
    }

    /**
     * 获得查询结果的状态
     * @return string
     */
    public function resultStatus(): string
    {
        return pg_result_error($this->result);
    }
}
