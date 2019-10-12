<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\realization\oracle\mode\driver;

use OCI_Lob;
use OCI_Collection;

/**
 * Class Oci
 * @notice 需要启用扩展ext-oci8
 * @package fize\db\realization\oracle\mode\driver
 */
class Oci
{

    /**
     * @var resource Oracle连接
     */
    protected $connection;

    /**
     * @var resource
     */
    protected $statement;

    /**
     * @var resource 描述符
     */
    protected $descriptor;

    /**
     * @var resource 游标
     */
    protected $cursor;

    /**
     * 将PHP数组var_array绑定到Oracleplaceholder名称，该名称指向Oracle PL/SQLarray。它是用于输入还是用于输出将在运行时确定。
     * @param $name
     * @param $var_array
     * @param int $max_table_length
     * @param int $type
     * @return bool
     */
    public function bindArrayByName($name, &$var_array, $max_table_length = -1, $type = 96)
    {
        return oci_bind_array_by_name($this->statement, $name, $var_array, $max_table_length, $type);
    }

    /**
     * 绑定一个 PHP 变量到一个 Oracle 位置标志符
     * @param $bv_name
     * @param $variable
     * @param int $maxlength
     * @param int $type
     * @return bool
     */
    public function bindByName($bv_name, &$variable, $maxlength = -1, $type = 1)
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
     * 返回Oracle客户端库版本
     * @return string
     */
    public function clientVersion()
    {
        return oci_client_version();
    }

    /**
     * 关闭 Oracle 连接
     * @return bool
     */
    public function close()
    {
        return oci_close($this->connection);
    }

    /**
     * 提交未执行的事务处理
     * @return bool
     */
    public function commit()
    {
        return oci_commit($this->connection);
    }

    /**
     * 建立一个到 Oracle 服务器的连接
     * @param string $username
     * @param string $password
     * @param string $connection_string
     * @param string $character_set
     * @param string $session_mode
     * @return resource
     */
    public function connect($username, $password, $connection_string = null, $character_set = null, $session_mode = null)
    {
        return oci_connect($username, $password, $connection_string, $character_set, $session_mode);
    }

    /**
     * 在 SELECT 中使用 PHP 变量作为定义的步骤
     * @param $statement
     * @param $column_name
     * @param $variable
     * @param int $type
     * @return bool
     */
    public function defineByName($statement, $column_name, &$variable, $type = 1)
    {
        return oci_define_by_name($statement, $column_name, $variable, $type);
    }

    /**
     * 返回上一个错误
     * @param resource $resource
     * @return array
     */
    public function error($resource = null)
    {
        return oci_error($resource);
    }

    /**
     * 执行一条语句
     * @param int $mode
     * @return bool
     */
    public function execute($mode = 32)
    {
        return oci_execute($this->statement, $mode);
    }

    /**
     * 获取结果数据的所有行到一个数组
     * @param array $output
     * @param int $skip
     * @param int $maxrows
     * @param int $flags
     * @return false|int
     */
    public function fetchAll(array &$output, $skip = 0, $maxrows = -1, $flags = 16)
    {
        return oci_fetch_all($this->statement, $output, $skip, $maxrows, $flags);
    }

    /**
     * 以关联数组或数字数组的形式返回查询的下一行
     * @param null $mode
     * @return array
     */
    public function fetchArray($mode = null)
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
    public function fieldIsNull($field )
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
     * 释放描述符
     * @return bool
     */
    public function freeDescriptor()
    {
        return oci_free_descriptor($this->descriptor);
    }

    /**
     * 释放预备语句
     * @return bool
     */
    public function freeStatement()
    {
        return oci_free_statement($this->statement);
    }

    /**
     * 从具有Oracle数据库12c隐式结果集的父语句资源中返回下一个子语句资源
     * @return resource
     */
    public function getImplicitResultset()
    {
        return oci_get_implicit_resultset($this->statement);
    }

    /**
     * 打开或关闭内部调试输出
     * @param $onoff
     */
    public static function internalDebug($onoff)
    {
        oci_internal_debug($onoff);
    }

    /**
     * 复制大对象副本
     * @param OCI_Lob $lob_to
     * @param OCI_Lob $lob_from
     * @param int $length
     * @return bool
     */
    public static function lobCopy(OCI_Lob $lob_to, OCI_Lob $lob_from, $length = 0)
    {
        return oci_lob_copy($lob_to, $lob_from, $length);
    }

    /**
     * 判断两个大对象副本是否相等
     * @param OCI_Lob $lob1
     * @param OCI_Lob $lob2
     * @return bool
     */
    public static function lobIsEqual(OCI_Lob $lob1, OCI_Lob $lob2)
    {
        return oci_lob_is_equal($lob1, $lob2);
    }

    /**
     * 分配新的 collection 对象
     * @param $tdo
     * @param null $schema
     * @return OCI_Collection
     */
    public function newCollection($tdo, $schema = null)
    {
        return oci_new_collection($this->connection, $tdo, $schema);
    }

    /**
     * 建定一个到 Oracle 服务器的新连接
     * @param $username
     * @param $password
     * @param null $connection_string
     * @param null $character_set
     * @param null $session_mode
     * @return false|resource
     */
    public static function newConnect($username, $password, $connection_string = null, $character_set = null, $session_mode = null)
    {
        return oci_new_connect($username, $password, $connection_string, $character_set, $session_mode);
    }

    /**
     * 分配并返回一个新的游标
     * @return resource
     */
    public function newCursor()
    {
        return oci_new_cursor($this->connection);
    }

    /**
     * 初始化一个新的空 LOB 或 FILE 描述符
     * @param int $type
     * @return OCI_Lob
     */
    public function newDescriptor($type = 50)
    {
        return oci_new_descriptor($this->connection, $type);
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
     * 配置 Oracle 语句预备执行
     * @param string $query SQL语句
     * @return resource
     */
    public function parse($query)
    {
        return oci_parse($this->connection, $query);
    }

    /**
     * 修改 Oracle 用户的密码
     * @param $username
     * @param $old_password
     * @param $new_password
     * @return bool
     */
    public function passwordChange($username, $old_password, $new_password)
    {
        return oci_password_change($this->connection, $username, $old_password, $new_password);
    }

    /**
     * 使用一个持久连接连到 Oracle 数据库
     * @param $username
     * @param $password
     * @param null $connection_string
     * @param null $character_set
     * @param null $session_mode
     * @return resource
     */
    public function pconnect($username, $password, $connection_string = null, $character_set = null, $session_mode = null)
    {
        return oci_pconnect($username, $password, $connection_string, $character_set, $session_mode);
    }

    /**
     * 为Oracle数据库TAF注册一个用户定义的回调函数
     * @param mixed $callback_fn
     * @return bool
     */
    public function registerTafCallback($callback_fn)
    {
        return oci_register_taf_callback($this->connection, $callback_fn);
    }

    /**
     * 返回所取得行中字段的值
     * @param $field
     * @return mixed
     */
    public function result($field)
    {
        return oci_result($this->statement, $field);
    }

    /**
     * 回滚未提交的事务
     * @return bool
     */
    public function rollback()
    {
        return oci_rollback($this->connection);
    }

    /**
     * 返回服务器版本信息
     * @return string
     */
    public function serverVersion()
    {
        return oci_server_version($this->connection);
    }

    /**
     * 设置动作名称
     * @param $action_name
     * @return bool
     */
    public function setAction($action_name)
    {
        return oci_set_action($this->connection, $action_name);
    }

    /**
     * 设置数据库调用的毫秒超时
     * @param $time_out
     * @return mixed
     */
    public function setCallTimeout($time_out)
    {
        return oci_set_call_timeout($this->connection, $time_out);
    }

    /**
     * 设置客户端标识符
     * @param $client_identifier
     * @return bool
     */
    public function setClientIdentifier($client_identifier)
    {
        return oci_set_client_identifier($this->connection, $client_identifier);
    }

    /**
     * 设置客户端信息
     * @param $client_info
     * @return bool
     */
    public function setClientInfo($client_info)
    {
        return oci_set_client_info($this->connection, $client_info);
    }

    /**
     * 设置数据库操作
     * @param $dbop
     * @return mixed
     */
    public function setDbOperation($dbop)
    {
        return oci_set_db_operation($this->connection, $dbop);
    }

    /**
     * 设置数据库版本
     * @param $edition
     * @return bool
     */
    public static function setEdition($edition)
    {
        return oci_set_edition($edition);
    }

    /**
     * 设置模块名称
     * @param $module_name
     * @return bool
     */
    public function setModuleName($module_name)
    {
        return oci_set_module_name($this->connection, $module_name);
    }

    /**
     * 设置预提取行数
     * @param $rows
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

    /**
     * 取消注册Oracle数据库TAF的用户定义回调函数
     * @return bool
     */
    public function unregisterTafCallback()
    {
        return oci_unregister_taf_callback($this->connection);
    }
}