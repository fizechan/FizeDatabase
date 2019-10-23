<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace fize\db\middleware\driver;


use Exception;


/**
 * ODBC驱动类
 * @notice ODBC的SQL预处理语句对中文支持跟ODBC驱动有关，例如{MySQL ODBC 5.3 ANSI Driver}、{MySQL ODBC 5.3 Unicode Driver}。
 * @notice 如果发现中文乱码问题，可以尝试替换驱动。
 * @package fize\db\middleware\driver
 */
class Odbc
{

    /**
     * 当前连接标识符
     * @var resource
     */
    private $connection = null;

    /**
     * 当前结果集标识符
     * @var resource
     */
    private $result = null;

    /**
     * 构造
     * @see https://www.connectionstrings.com/ 可用DSN参见
     * @param string $dsn 连接的数据库源名称。另外，一个无DSN连接字符串可以使用。
     * @param string $user 用户名
     * @param string $pwd 密码
     * @param int $cursor_type 可选SQL_CUR_USE_IF_NEEDED | SQL_CUR_USE_ODBC | SQL_CUR_USE_DRIVER
     * @param bool $pconnect 是否使用长链接，默认false
     * @throws Exception
     */
    public function __construct($dsn, $user, $pwd, $cursor_type = null, $pconnect = false)
    {
        if ($pconnect) {
            $this->connection = odbc_pconnect($dsn, $user, $pwd, $cursor_type);
        } else {
            $this->connection = odbc_connect($dsn, $user, $pwd, $cursor_type);
        }
        if (!$this->connection) {
            throw new Exception("SQL state " . odbc_error() . ":" . iconv('GB2312', 'UTF-8', odbc_errormsg()));
        }
    }

    /**
     * 析构
     * 由于调用closeAll时会关闭所有链接，导致影响close的执行，故析构时并没有将链接关闭，需要时，请手动关闭。
     */
    public function __destruct()
    {
    }

    /**
     * 获取或设置自动提交状态
     * @param bool $OnOff 当带$OnOff时，True表示开始自动提交，False表示关闭自动提交，null标识获取状态
     * @return int 返回状态时开启为非0值，关闭为0值，设置状态则返回结果
     * @throws Exception
     */
    public function autocommit($OnOff = null)
    {
        if ($OnOff === null) {
            $rst = odbc_autocommit($this->connection);
        } else {
            $rst = odbc_autocommit($this->connection, $OnOff);
        }
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return $rst;
    }

    /**
     * 对结果集设置处理二进制数据的模式
     * @param int $mode 指定模式， 可选值 ODBC_BINMODE_PASSTHRU | ODBC_BINMODE_RETURN | ODBC_BINMODE_CONVERT
     * @return bool
     * @throws Exception
     */
    public function binmode($mode)
    {
        $rst = odbc_binmode($this->result, $mode);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return $rst;
    }

    /**
     * 关闭所有ODBC连接
     */
    public static function closeAll()
    {
        odbc_close_all();
    }

    /**
     * 关闭当前ODBC连接
     */
    public function close()
    {
        if ($this->connection != null && get_resource_type($this->connection) == "odbc link") {
            odbc_close($this->connection);
        }
        $this->connection = null;
    }

    /**
     * 列出给定表的列和相关联的特权
     * @todo 20170613测试，一直为空，不知为何。
     * @param string $qualifier 限定符。
     * @param string $owner 所有人，支持%(零个或多个字符)_(1个字符)
     * @param string $table_name 表名，支持%(零个或多个字符)_(1个字符)
     * @param string $column_name 列名，支持%(零个或多个字符)_(1个字符)
     * @return resource
     * @throws Exception
     */
    public function columnprivileges($qualifier, $owner, $table_name, $column_name)
    {
        $rst = odbc_columnprivileges($this->connection, $qualifier, $owner, $table_name, $column_name);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        $this->result = $rst;
        return $this->result;
    }

    /**
     * 列出指定表中的列名。
     * @param string $qualifier 限定符。
     * @param string $schema 所有人，支持%(零个或多个字符)_(1个字符)
     * @param string $table_name $table_name 表名，支持%(零个或多个字符)_(1个字符)
     * @param string $column_name 列名，支持%(零个或多个字符)_(1个字符)
     * @return resource
     * @throws Exception
     */
    public function columns($qualifier = null, $schema = null, $table_name = null, $column_name = null)
    {
        $rst = odbc_columns($this->connection, $qualifier, $schema, $table_name, $column_name);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        $this->result = $rst;
        return $this->result;
    }

    /**
     * 提交ODBC事务
     * @throws Exception
     */
    public function commit()
    {
        $rst = odbc_commit($this->connection);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
    }

    /**
     * 返回结果游标名称
     * @return string
     */
    public function cursor()
    {
        return odbc_cursor($this->result);
    }

    /**
     * 返回当前连接的信息。
     * @param int $fetch_type 可选SQL_FETCH_FIRST | SQL_FETCH_NEXT
     * @return array
     * @throws Exception
     */
    public function dataSource($fetch_type)
    {
        $rst = odbc_data_source($this->connection, $fetch_type);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return $rst;
    }

    /**
     * 执行一个SQL语句，返回结果集
     * @param string $query_string SQL语句
     * @param int $flags 此参数目前没有使用
     * @return resource
     * @throws Exception
     */
    public function exec($query_string, $flags = null)
    {
        $rst = odbc_exec($this->connection, $query_string, $flags);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        $this->result = $rst;
        return $this->result;
    }

    /**
     * 执行当前预处理语句
     * @param array $parameters_array 可选的参数
     * @throws Exception
     */
    public function execute(array $parameters_array = null)
    {
        $rst = odbc_execute($this->result, $parameters_array);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
    }

    /**
     * 以数组形式遍历结果集
     * @param int $rownumber 指定要检索的行数
     * @return array
     */
    public function fetchArray($rownumber = null)
    {
        return odbc_fetch_array($this->result, $rownumber);
    }

    /**
     * 遍历结果集到指定数组
     * @param array $result_array 结果集将添加到该数组
     * @param int $rownumber 指定要检索的行数
     * @return int 返回结果行数
     * @throws Exception
     */
    public function fetchInto(array &$result_array, $rownumber = null)
    {
        $rst = odbc_fetch_into($this->result, $result_array, $rownumber);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
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
        return odbc_fetch_object($this->result, $rownumber);
    }

    /**
     * 移动结果集指针，使用该方法后，下次使用odbc_result()将返回下一行的结果
     * @param int $row_number 指定要检索的行数
     * @return bool 成功返回true，失败返回false
     */
    public function fetchRow($row_number = null)
    {
        return odbc_fetch_row($this->result, $row_number);
    }

    /**
     * 返回指定字段信息数组，其中信息如下：
     * index：字段所在位置，以1开始；
     * len：字段长；
     * name：字段名称；
     * scale：小数位；
     * type：类型；
     * @param mixed $index 可以是索引位(以1开始)或者字段名称
     * @return array
     * @throws Exception
     */
    public function field($index)
    {
        if (is_string($index)) {
            $index = odbc_field_num($this->result, $index);
            if ($index === false) {
                throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
            }
        }
        return [
            'index' => $index,
            'len' => odbc_field_len($this->result, $index),
            'name' => odbc_field_name($this->result, $index),
            'scale' => odbc_field_scale($this->result, $index),
            'type' => odbc_field_type($this->result, $index),
        ];
    }

    /**
     * 获取检索外键的列表。
     * @param string $pk_qualifier 主键限定符。
     * @param string $pk_owner 主键所有者。
     * @param string $pk_table 主键表。
     * @param string $fk_qualifier 外键限定符。
     * @param string $fk_owner 外键所有者。
     * @param string $fk_table 外键表。
     * @return resource 返回结果集
     * @throws Exception
     */
    public function foreignkeys($pk_qualifier, $pk_owner, $pk_table, $fk_qualifier, $fk_owner, $fk_table)
    {
        $rst = odbc_foreignkeys($this->connection, $pk_qualifier, $pk_owner, $pk_table, $fk_qualifier, $fk_owner, $fk_table);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        $this->result = $rst;
        return $this->result;
    }

    /**
     * 释放当前结果内存
     * @return bool
     */
    public function freeResult()
    {
        $result = odbc_free_result($this->result);
        $this->result = null;
        return $result;
    }

    /**
     * 检索有关数据源支持的数据类型的信息。
     * @param int $data_type 数据类型，可用于将信息限制为单个数据类型。
     * @return resource 返回结果集，错误时返回false
     */
    public function gettypeinfo($data_type = null)
    {
        $this->result = odbc_gettypeinfo($this->connection, $data_type);
        return $this->result;
    }

    /**
     * 设置允许的最长字段列处理
     * @param int $length 最长字段长度
     * @return bool
     */
    public function longreadlen($length)
    {
        return odbc_longreadlen($this->result, $length);
    }

    /**
     * 对于多个结果集，将指针移到下个结果集
     * @return bool
     */
    public function nextResult()
    {
        return odbc_next_result($this->result);
    }

    /**
     * 返回结果中的列数
     * @return int
     */
    public function numFields()
    {
        return odbc_num_fields($this->result);
    }

    /**
     * 返回结果中的行(记录)数
     * @return int
     */
    public function numRows()
    {
        return odbc_num_rows($this->result);
    }

    /**
     * 设置一个预处理语句
     * @param string $query_string 预处理语句，支持问号占位符
     * @return resource 该返回值，可以使用execute()进行实际执行
     * @throws Exception
     */
    public function prepare($query_string)
    {
        $rst = odbc_prepare($this->connection, $query_string);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        $this->result = $rst;
        return $this->result;
    }

    /**
     * 获取指定表的主键
     * @param string $qualifier 限定符
     * @param string $owner 所有者
     * @param string $table 表名
     * @return resource 结果集
     */
    public function primarykeys($qualifier, $owner, $table)
    {
        $this->result = odbc_primarykeys($this->connection, $qualifier, $owner, $table);
        return $this->result;
    }

    /**
     * 检索参数到过程的信息
     * @deprecated 非常见用法，不建议使用
     * @param string $qualifier 限定符
     * @param string $owner 所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $proc 过程。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $column 列名。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @return resource 结果集
     */
    public function procedurecolumns($qualifier = null, $owner = null, $proc = null, $column = null)
    {
        $this->result = odbc_procedurecolumns($this->connection, $qualifier, $owner, $proc, $column);
        return $this->result;
    }

    /**
     * 获取存储在特定数据源中的过程列表。
     * @deprecated 非常见用法，不建议使用
     * @param string $qualifier 限定符
     * @param string $owner 所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $name 名称。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     */
    public function procedures($qualifier = null, $owner = null, $name = null)
    {
        $this->result = odbc_procedures($this->connection, $qualifier, $owner, $name);
    }

    /**
     * 将结果以HTML表格形式打印出来。
     * @param string $format 附加的整体表格格式。
     * @return int 返回结果集大小，失败返回false
     * @throws Exception
     */
    public function resultAll($format = null)
    {
        $rst = odbc_result_all($this->result, $format);
        if($rst === false){
            throw new Exception(odbc_error($this->connection), odbc_errormsg($this->connection));
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
        return odbc_result($this->result, $field);
    }

    /**
     * 回滚当前事务
     * @return bool
     */
    public function rollback()
    {
        return odbc_rollback($this->connection);
    }

    /**
     * 改变属性
     * @param int $function 为1时改变链接属性，为2改变结果集属性
     * @param int $option 属性名
     * @param int $param 属性值
     * @return bool
     */
    public function setoption($function, $option, $param)
    {
        if ($function == 1) {
            return odbc_setoption($this->connection, 1, $option, $param);
        } else {
            return odbc_setoption($this->result, 2, $option, $param);
        }
    }

    /**
     * 获取指定表的索引
     * @param int $type 指定类型，可选SQL_BEST_ROWID | SQL_ROWVER特殊值
     * @param string $qualifier 限定符
     * @param string $owner 所有者
     * @param string $table 表名
     * @param int $scope 命令结果集的作用域。
     * @param int $nullable null选项
     * @return resource 结果集
     */
    public function specialcolumns($type, $qualifier, $owner, $table, $scope, $nullable)
    {
        $this->result = odbc_specialcolumns($this->connection, $type, $qualifier, $owner, $table, $scope, $nullable);
        return $this->result;
    }

    /**
     * 检索表的统计信息
     * @param string $qualifier 限定符
     * @param string $owner 所有者
     * @param string $table_name 表名
     * @param int $unique 特有属性
     * @param int $accuracy 准确性
     * @return resource 结果集
     */
    public function statistics($qualifier, $owner, $table_name, $unique, $accuracy)
    {
        $this->result = odbc_statistics($this->connection, $qualifier, $owner, $table_name, $unique, $accuracy);
        return $this->result;
    }

    /**
     * 列出与每个表相关联的表和特权。
     * @param string $qualifier 限定符
     * @param string $owner 所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $name 名称。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @return resource 结果集
     */
    public function tableprivileges($qualifier, $owner, $name)
    {
        $this->result = odbc_tableprivileges($this->connection, $qualifier, $owner, $name);
        return $this->result;
    }

    /**
     * 获取存储在特定数据源中的表名列表。
     * @param string $qualifier 限定符
     * @param string $owner 所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $name 名称。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $types 指定类型，"'TABLE','VIEW'" or "TABLE, VIEW"
     * @return resource 结果集
     */
    public function tables($qualifier = null, $owner = null, $name = null, $types = null)
    {
        $this->result = odbc_tables($this->connection, $qualifier, $owner, $name, $types);
        return $this->result;
    }
}