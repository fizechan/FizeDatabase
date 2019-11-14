<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\realization\oracle\mode\driver\oci;

/**
 * Oci预处理语句
 */
class Statement
{

    /**
     * @var resource 预处理语句
     */
    protected $statement;

    /**
     * @var int 执行模式
     */
    public $mode = 32;

    /**
     * 构造
     * @param resource $statement 预处理语句资源对象
     * @param int $mode 执行模式
     */
    public function __construct(&$statement, $mode = null)
    {
        $this->statement = $statement;
        if(!is_null($mode)) {
            $this->mode = $mode;
        }
    }

    /**
     * 析构时释放预处理语句
     */
    public function __destruct()
    {
        $this->freeStatement();
    }

    /**
     * 返回资源原型，用于原始操作
     * @return resource
     */
    public function prototype()
    {
        return $this->statement;
    }

    /**
     * 将PHP数组var_array绑定到Oracleplaceholder名称，该名称指向Oracle PL/SQLarray。它是用于输入还是用于输出将在运行时确定。
     * @param string $name 占位符
     * @param array $var_array 数组
     * @param int $max_table_length 设置传入和结果数组的最大长度。
     * @param int $max_item_length 设置数组项的最大长度。如果未指定或等于-1,oci_bind_array_by_name()将在传入数组中查找longestelement，并将其用作最大长度。
     * @param int $type 应该用于设置PL/SQL数组项的类型
     * @return bool
     */
    public function bindArrayByName($name, &$var_array, $max_table_length, $max_item_length = -1, $type = 96)
    {
        return oci_bind_array_by_name($this->statement, $name, $var_array, $max_table_length, $max_item_length, $type);
    }

    /**
     * 绑定一个 PHP 变量到一个 Oracle 位置标志符
     * @param string $bv_name 占位符
     * @param mixed $variable 变量
     * @param int $maxlength 设置最大长度。
     * @param int $type 要使用什么样的描述符
     * @return bool
     */
    public function bindByName($bv_name, $variable, $maxlength = -1, $type = 1)
    {
        return oci_bind_by_name($this->statement, $bv_name, $variable, $maxlength, $type);
    }

    /**
     * 中断游标读取数据
     * @return bool
     */
    public function cancel()
    {
        return oci_cancel($this->statement);
    }

    /**
     * 在 SELECT 中使用 PHP 变量作为定义的步骤
     * @param string $column_name 列名
     * @param mixed $variable 要绑定的变量
     * @param int $type 列类型
     * @return bool
     */
    public function defineByName($column_name, &$variable, $type = null)
    {
        if(is_null($type)) {
            return oci_define_by_name($this->statement, $column_name, $variable);
        }
        return oci_define_by_name($this->statement, $column_name, $variable, $type);
    }

    /**
     * 执行一条语句
     * @param int $mode 执行模式
     * @return bool
     */
    public function execute($mode = null)
    {
        $mode = is_null($mode) ? $this->mode : $mode;
        return oci_execute($this->statement, $mode);
    }

    /**
     * 获取结果数据的所有行到一个数组
     * @param array $output 得到的数据数组
     * @param int $skip 偏移量
     * @param int $maxrows 返回数量，-1表示不显示
     * @param int $flags 标识参数
     * @return int 失败时返回false
     */
    public function fetchAll(&$output, $skip = 0, $maxrows = -1, $flags = 16)
    {
        return oci_fetch_all($this->statement, $output, $skip, $maxrows, $flags);
    }

    /**
     * 以关联数组或数字数组的形式返回查询的下一行
     * @param int $mode 模式
     * @return array
     */
    public function fetchArray($mode = 3)
    {
        return oci_fetch_array($this->statement, $mode);
    }

    /**
     * 以关联数组的形式返回查询的下一行
     * @return array
     */
    public function fetchAssoc()
    {
        return oci_fetch_assoc($this->statement);
    }

    /**
     * 以对象形式返回查询的下一行
     * @return object
     */
    public function fetchObject()
    {
        return oci_fetch_object($this->statement);
    }

    /**
     * 以数字数组的形式返回查询的下一行
     * @return array
     */
    public function fetchRow()
    {
        return oci_fetch_row($this->statement);
    }

    /**
     * 获取下一行（对于 SELECT 语句）到内部结果缓冲区。
     * @return bool
     */
    public function fetch()
    {
        return oci_fetch($this->statement);
    }

    /**
     * 检查字段是否为 NULL
     * @param mixed $field 字段的索引或字段名（大写字母）。
     * @return bool
     */
    public function fieldIsNull($field)
    {
        return oci_field_is_null($this->statement, $field);
    }

    /**
     * 返回与字段数字索引（从 1 开始）相对应的字段名
     * @param int $field 字段数字索引（从 1 开始）
     * @return string
     */
    public function fieldName($field)
    {
        return oci_field_name($this->statement, $field);
    }

    /**
     * 返回字段精度
     * @param int $field 索引（从 1 开始)
     * @return int
     */
    public function fieldPrecision($field)
    {
        return oci_field_precision($this->statement, $field);
    }

    /**
     * 返回字段范围
     * @param int $field 索引（从 1 开始)
     * @return int
     */
    public function fieldScale($field)
    {
        return oci_field_scale($this->statement, $field);
    }

    /**
     * 返回字段大小
     * @param int $field 索引（从 1 开始)
     * @return int
     */
    public function fieldSize($field)
    {
        return oci_field_size($this->statement, $field);
    }

    /**
     * 返回字段的原始 Oracle 数据类型
     * @param int $field 索引（从 1 开始)
     * @return int
     */
    public function fieldTypeRaw($field)
    {
        return oci_field_type_raw($this->statement, $field);
    }

    /**
     * 返回字段的数据类型
     * @param int $field 索引（从 1 开始)
     * @return mixed
     */
    public function fieldType($field)
    {
        return oci_field_type($this->statement, $field);
    }

    /**
     * 释放预备语句
     * @return bool
     */
    public function freeStatement()
    {
        if(!$this->statement) {
            return true;
        }
        $result = oci_free_statement($this->statement);
        if($result) {
            $this->statement = null;
        }
        return $result;
    }

    /**
     * 从具有Oracle数据库12c隐式结果集的父语句资源中返回下一个子语句资源
     * @return $this|false 没有下一个语句时返回false
     */
    public function getImplicitResultset()
    {
        $resource = oci_get_implicit_resultset($this->statement);
        if($resource === false) {
            return false;
        }
        return new static($resource);
    }

    /**
     * 返回结果列的数目
     * @return int
     */
    public function numFields()
    {
        return oci_num_fields($this->statement);
    }

    /**
     * 返回语句执行后受影响的行数
     * @return int
     */
    public function numRows()
    {
        return oci_num_rows($this->statement);
    }

    /**
     * 返回所取得行中字段的值
     * @param mixed $field 字段名或下标(从1开始)
     * @return mixed
     */
    public function result($field)
    {
        return oci_result($this->statement, $field);
    }

    /**
     * 设置预提取行数
     * @param int $rows 预提取行数
     * @return bool
     */
    public function setPrefetch($rows)
    {
        return oci_set_prefetch($this->statement, $rows);
    }

    /**
     * 返回 OCI 语句的类型
     * @return string
     */
    public function statementType()
    {
        return oci_statement_type($this->statement);
    }
}