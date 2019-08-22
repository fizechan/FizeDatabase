<?php


namespace fize\db\realization\pgsql\mode\driver;


/**
 * PostgreSQL数据库
 */
class Pgsql
{

    /**
     * @var resource 连接
     */
    protected $connection = null;

    /**
     * @var resource 结果集
     */
    protected $result = null;

    /**
     * @var resource 大型对象
     */
    protected $largeObject = null;

    /**
     * Pgsql constructor.
     * @param string $connection_string 连接字符串
     */
    public function __construct($connection_string)
    {
        $this->connection = self::connect($connection_string);
    }

    public function __destruct()
    {
        if($this->connection) {
            $this->close();
        }
    }

    /**
     * 返回受影响的记录数目
     * @return int
     */
    public function affectedRows()
    {
        return pg_affected_rows($this->result);
    }

    /**
     * 取消异步查询
     * @return bool
     */
    public function cancelQuery()
    {
        return pg_cancel_query($this->connection);
    }

    /**
     * 取得客户端编码方式
     * @return string
     */
    public function clientEncoding()
    {
        return pg_client_encoding($this->connection);
    }

    /**
     * 关闭连接
     * @return bool
     */
    public function close()
    {
        return pg_close($this->connection);
    }

    /**
     * 正在进行尝试轮询 PostgreSQL 链接状态
     * @return int 返回常量 PGSQL_POLLING_FAILED, PGSQL_POLLING_READING, PGSQL_POLLING_WRITING, PGSQL_POLLING_OK, 或者 PGSQL_POLLING_ACTIVE
     */
    public function connectPoll()
    {
        return pg_connect_poll($this->connection);
    }

    /**
     * 打开一个 PostgreSQL 连接
     * @param string $connection_string 连接字符串
     * @return resource
     */
    public static function connect($connection_string)
    {
        return pg_connect($connection_string);
    }

    /**
     * 获知连接是否为忙
     * @return bool
     */
    public function connectionBusy()
    {
        return pg_connection_busy($this->connection);
    }

    /**
     * 重置连接（再次连接）
     * @return bool
     */
    public function connectionReset()
    {
        return pg_connection_reset($this->connection);
    }

    /**
     * 获得连接状态
     * @return int 可能的状态为 PGSQL_CONNECTION_OK 和 PGSQL_CONNECTION_BAD
     */
    public function connectionStatus()
    {
        return pg_connection_status($this->connection);
    }

    /**
     * 读取连接上的输入
     * @notice 不建议使用
     * @return bool
     */
    public function consumeInput()
    {
        return pg_consume_input($this->connection);
    }

    /**
     * 将关联的数组值转换为适合 SQL 语句的格式
     * @notice 此函数是实验性的
     * @param string $table_name 表名
     * @param array $assoc_array 键值对
     * @param int $options 常量PGSQL_CONV_IGNORE_DEFAULT, PGSQL_CONV_FORCE_NULL, PGSQL_CONV_IGNORE_NOT_NULL
     * @return array
     */
    public function convert($table_name, array $assoc_array, $options = 0)
    {
        return pg_convert($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 根据数组将记录插入表中
     * @param string $table_name 表名
     * @param array $rows 要插入的记录
     * @param string $delimiter 间隔符
     * @param string $null_as NULL值的替代
     * @return bool
     */
    public function copyFrom($table_name, array $rows, $delimiter = null, $null_as = null)
    {
        return pg_copy_from($this->connection, $table_name, $rows, $delimiter, $null_as);
    }

    /**
     * 将一个表拷贝到数组中
     * @param string $table_name 表名
     * @param string $delimiter 间隔符
     * @param string $null_as NULL值的替代
     * @return array
     */
    public function copyTo($table_name, $delimiter = null, $null_as = null)
    {
        return pg_copy_to($this->connection, $table_name, $delimiter, $null_as);
    }

    /**
     * 获得数据库名
     * @return string
     */
    public function dbname()
    {
        return pg_dbname($this->connection);
    }

    /**
     * 删除记录
     * @notice 此函数是实验性的
     * @param string $table_name 表名
     * @param array $assoc_array 以 field=>value 格式给出的条件
     * @param int $options 常量PGSQL_CONV_FORCE_NULL, PGSQL_DML_NO_CONV, PGSQL_DML_EXEC or PGSQL_DML_STRING
     * @return mixed
     */
    public function delete($table_name, array $assoc_array, $options = 512)
    {
        return pg_delete($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 与 PostgreSQL 后端同步
     * @return bool
     */
    public function endCopy()
    {
        return pg_end_copy($this->connection);
    }

    /**
     * 转义 bytea 类型的二进制数据
     * @param string $data 二进制字符串
     * @return string
     */
    public function escapeBytea($data)
    {
        return pg_escape_bytea($this->connection, $data);
    }

    /**
     * 转义用于插入文本字段的标识符
     * @param string $data 文本
     * @return string
     */
    public function escapeIdentifier($data)
    {
        return pg_escape_identifier($this->connection, $data);
    }

    /**
     * 转义用于插入文本字段的文字
     * @param string $data 文本
     * @return string
     */
    public function escapeLiteral($data)
    {
        return pg_escape_literal($this->connection, $data);
    }

    /**
     * 转义 text/char 类型的字符串
     * @param string $data 文本
     * @return string
     */
    public function escapeString($data)
    {
        return pg_escape_string($this->connection, $data);
    }

    /**
     * 发送一个请求来执行带有给定参数的准备好的语句，并等待结果
     * @param string $stmtname SQL预处理语句
     * @param array $params 绑定参数
     * @return resource
     */
    public function execute($stmtname, array $params)
    {
        $result = pg_execute($this->connection, $stmtname, $params);
        $this->result = $result;
        return $result;
    }

    /**
     * 以数组的形式获取特定结果列中的所有行
     * @param int $column 要从结果资源中检索的列号(从零开始)
     * @return array
     */
    public function fetchAllColumns($column = 0)
    {
        return pg_fetch_all_columns($this->result, $column);
    }

    /**
     * 从结果中提取所有行作为一个数组
     * @return array
     */
    public function fetchAll()
    {
        return pg_fetch_all($this->result);
    }

    /**
     * 提取一行作为数组
     * @param int $row 要从结果资源中检索的列号(从零开始)
     * @param int $result_type 常量PGSQL_ASSOC，PGSQL_NUM 和 PGSQL_BOTH
     * @return array
     */
    public function fetchArray($row = null, $result_type = 3)
    {
        return pg_fetch_array($this->result, $row, $result_type);
    }

    /**
     * 提取一行作为关联数组
     * @param int $row 要从结果资源中检索的列号(从零开始)
     * @return array
     */
    public function fetchAssoc($row = null)
    {
        return pg_fetch_assoc($this->result, $row);
    }

    /**
     * 提取一行作为对象
     * @param int $row 要从结果资源中检索的列号(从零开始)
     * @return object
     */
    public function fetchObject($row = null)
    {
        return pg_fetch_object($this->result, $row);
    }

    /**
     * 从结果资源中返回值
     * @param int $row 要从结果资源中检索的列号(从零开始)
     * @param mixed $field 字段名（字符串）或字段索引（整数）。
     * @return mixed
     */
    public function fetchResult($row, $field)
    {
        return pg_fetch_result($this->result, $row, $field);
    }

    /**
     * 提取一行作为枚举数组
     * @param int $row 要从结果资源中检索的列号(从零开始)
     * @return array
     */
    public function fetchRow($row = null)
    {
        return pg_fetch_row($this->result, $row);
    }

    /**
     * 测试字段是否为 NULL
     * @param int $row 要从结果资源中检索的列号(从零开始)
     * @param mixed $field 字段名（字符串）或字段索引（整数）。
     * @return int 为null返回1，不为null返回0
     */
    public function fieldIsNull($row, $field)
    {
        return pg_field_is_null($this->result, $row, $field);
    }

    /**
     * 返回字段的名字
     * @param int $field_number 字段编号从 0 开始
     * @return string
     */
    public function fieldName($field_number)
    {
        return pg_field_name($this->result, $field_number);
    }

    /**
     * 返回字段的编号
     * @param string $field_name 字段名
     * @return int 未找到或者出错时返回-1
     */
    public function fieldNum($field_name)
    {
        return pg_field_num($this->result, $field_name);
    }

    /**
     * 返回打印出来的长度
     * @param int $row_number 要从结果资源中检索的列号(从零开始)
     * @param string $field_name 字段名
     * @return int
     */
    public function fieldPrtlen($row_number, $field_name)
    {
        return pg_field_prtlen($this->result, $row_number, $field_name);
    }

    /**
     * 返回指定字段占用内部存储空间的大小
     * @param int $field_number 字段编号从 0 开始
     * @return int 字段大小为 -1 表示可变长度字段。如果出错本函数返回 FALSE
     */
    public function fieldSize($field_number)
    {
        return pg_field_size($this->result, $field_number);
    }

    /**
     * 返回tables字段的名称或oid
     * @param int $field_number 字段编号从 0 开始
     * @param bool $oid_only 默认情况下，返回字段所属的表名，但如果oid_only设置为TRUE，则返回oid。
     * @return mixed
     */
    public function fieldTable($field_number, $oid_only = false)
    {
        return pg_field_table($this->result, $field_number, $oid_only);
    }

    /**
     * 返回对应字段号的类型ID (OID)
     * @param int $field_number 字段编号从 0 开始
     * @return int
     */
    public function fieldTypeOid($field_number)
    {
        return pg_field_type_oid($this->result, $field_number);
    }

    /**
     * 返回相应字段的类型名称
     * @param int $field_number 字段编号从 0 开始
     * @return string
     */
    public function fieldType($field_number)
    {
        return pg_field_type($this->result, $field_number);
    }

    /**
     * 刷新链接中已处理的数据查询
     * @return mixed
     */
    public function flush()
    {
        return pg_flush($this->connection);
    }

    /**
     * 释放查询结果占用的内存
     * @return bool
     */
    public function freeResult()
    {
        return pg_free_result($this->result);
    }

    /**
     * Ping 数据库连接
     * @param int $result_type 返回类型
     * @return array 通告消息
     */
    public function getNotify($result_type = null)
    {
        return pg_get_notify($this->connection, $result_type);
    }

    /**
     * 取得后端（数据库服务器进程）的 PID
     * @return int
     */
    public function getPid()
    {
        return pg_get_pid($this->connection);
    }

    /**
     * 取得异步查询结果
     * @return resource
     */
    public function get_result()
    {
        return pg_get_result($this->connection);
    }

    /**
     * 返回和某连接关联的主机名
     * @return string
     */
    public function host()
    {
        return pg_host($this->connection);
    }

    /**
     * 将数组插入到表中
     * @param string $table_name 表名
     * @param array $assoc_array 数组
     * @param int $options 常量PGSQL_CONV_OPTS, PGSQL_DML_NO_CONV, PGSQL_DML_EXEC, PGSQL_DML_ASYNC or PGSQL_DML_STRING
     * @return mixed
     */
    public function insert($table_name, array $assoc_array, $options = 512)
    {
        return pg_insert($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 得到某连接的最后一条错误信息
     * @return string
     */
    public function lastError()
    {
        return pg_last_error($this->connection);
    }

    /**
     * 返回 PostgreSQL 服务器最新一条公告信息
     * @return string
     */
    public function lastNotice()
    {
        return pg_last_notice($this->connection);
    }

    /**
     * 返回上一个对象的 oid
     * @return string
     */
    public function lastOid()
    {
        return pg_last_oid($this->result);
    }

    /**
     * 关闭一个大型对象
     * @return bool
     */
    public function loClose()
    {
        return pg_lo_close($this->largeObject);
    }

    /**
     * 新建一个大型对象
     * @return int
     */
    public function loCreate()
    {
        return pg_lo_create($this->connection);
    }

    /**
     * 将大型对象导出到文件
     * @param int $oid 要导出的数据库里的大型对象的 OID
     * @param string $pathname 要导出的数据库里的大型对象的文件在客户端上完整路径和文件名
     * @return bool
     */
    public function loExport($oid, $pathname)
    {
        return pg_lo_export($this->connection, $oid, $pathname);
    }

    /**
     * 将文件导入为大型对象
     * @param string $pathname 变量指明了要导入为大型对象的文件名
     * @param mixed $object_id 尝试用该对象ID创建
     * @return int
     */
    public function loImport($pathname, $object_id = null)
    {
        return pg_lo_import($this->connection, $pathname, $object_id);
    }

    /**
     * 打开一个大型对象
     * @param int $oid 指定了有效的大型对象的 oid
     * @param string $mode 可以为 "r"，"w" 或者 "rw"。
     * @return resource 失败则返回 FALSE
     */
    public function loOpen($oid, $mode)
    {
        $large_object = pg_lo_open($this->connection, $oid, $mode);
        $this->largeObject = $large_object;
        return $large_object;
    }

    /**
     * 读入整个大型对象并直接发送给浏览器
     * @return int
     */
    public function loReadAll()
    {
        return pg_lo_read_all($this->largeObject);
    }

    /**
     * 从大型对象中读入数据
     * @param int $len 读入最多 len 字节的数据
     * @return string
     */
    public function loRead($len)
    {
        return pg_lo_read($this->largeObject, $len);
    }

    /**
     * 移动大型对象中的指针
     * @param int $offset 偏移量
     * @param int $whence 参数为 PGSQL_SEEK_SET，PGSQL_SEEK_CUR 或 PGSQL_SEEK_END
     * @return bool
     */
    public function loSeek($offset, $whence = 1)
    {
        return pg_lo_seek($this->largeObject, $offset, $whence);
    }

    /**
     * 返回大型对象的当前指针位置
     * @return int
     */
    public function loTell()
    {
        return pg_lo_tell($this->largeObject);
    }

    /**
     * 截断大对象
     * @param int $size 要截断的字节数
     * @return bool
     */
    public function loTruncate($size)
    {
        return pg_lo_truncate($this->largeObject, $size);
    }

    /**
     * 删除一个大型对象
     * @param int $oid 对象ID
     * @return bool
     */
    public function loUnlink($oid)
    {
        return pg_lo_unlink($this->connection, $oid);
    }

    /**
     * 向大型对象写入数据
     * @param string $data 要写入的数据
     * @return int
     */
    public function loWrite($data)
    {
        return pg_lo_write($this->largeObject, $data);
    }

    /**
     * 获得表的元数据
     * @param string $table_name 表名
     * @return array
     */
    public function metaData($table_name)
    {
        return pg_meta_data($this->connection, $table_name);
    }

    /**
     * 返回字段的数目
     * @return int
     */
    public function numFields()
    {
        return pg_num_fields($this->result);
    }

    /**
     * 返回行的数目
     * @return int
     */
    public function numRows()
    {
        return pg_num_rows($this->result);
    }

    /**
     * 获得和连接有关的选项
     * @return string
     */
    public function options()
    {
        return pg_options($this->connection);
    }

    /**
     * 查找服务器的当前参数设置
     * @param string $param_name 参数名
     * @return string
     */
    public function parameterStatus($param_name)
    {
        return pg_parameter_status($this->connection, $param_name);
    }

    /**
     * 打开一个持久的 PostgreSQL 连接
     * @param string $connection_string 连接字符串
     * @param int $connect_type PGSQL_CONNECT_FORCE_NEW强制新连接
     * @return resource
     */
    public static function pconnect($connection_string, $connect_type = null)
    {
        return pg_pconnect($connection_string, $connect_type);
    }

    /**
     * Ping 数据库连接
     * @return bool
     */
    public function ping()
    {
        return pg_ping($this->connection);
    }

    /**
     * 返回该连接的端口号
     * @return int
     */
    public function port()
    {
        return pg_port($this->connection);
    }

    /**
     * 提交一个请求，用给定的参数创建一个准备好的语句，并等待完成
     * @param string $stmtname 名称
     * @param string $query 语句
     * @return resource
     */
    public function prepare($stmtname, $query)
    {
        return pg_prepare($this->connection, $stmtname, $query);
    }

    /**
     * 向 PostgreSQL 后端发送以 NULL 结尾的字符串
     * @param string $data 数据
     * @return bool
     */
    public function putLine($data)
    {
        return pg_put_line($this->connection, $data);
    }

    /**
     * 向服务器提交一个命令并等待结果，同时能够独立于SQL命令文本传递参数
     * @param string $query SQL语句，支持占位符
     * @param array $params 绑定参数
     * @return resource
     */
    public function queryParams($query, array $params)
    {
        return pg_query_params($this->connection, $query, $params);
    }

    /**
     * 执行查询
     * @param string $query SQL语句
     * @return resource
     */
    public function query($query)
    {
        return pg_query($this->connection, $query);
    }

    /**
     * 返回错误报告的单个字段
     * @param int $fieldcode 指定常量配置
     * @return string
     */
    public function resultErrorField($fieldcode)
    {
        return pg_result_error_field($this->result, $fieldcode);
    }

    /**
     * 获得查询结果的错误信息
     * @return string
     */
    public function resultError()
    {
        return pg_result_error($this->result);
    }

    /**
     * 在结果资源中设定内部行偏移量
     * @param int $offset 偏移量
     * @return bool
     */
    public function resultSeek($offset)
    {
        return pg_result_seek($this->result, $offset);
    }

    /**
     * 获得查询结果的状态
     * @return string
     */
    public function resultStatus()
    {
        return pg_result_error($this->result);
    }

    /**
     * 选择记录
     * @param string $table_name 表名
     * @param array $assoc_array 条件数组
     * @param int $options 选项常量
     * @return mixed
     */
    public function select($table_name, array $assoc_array, $options = 512)
    {
        return pg_select($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 发送一个请求来执行带有给定参数的准备好的语句，而不需要等待结果
     * @param string $stmtname 预处理语句名称
     * @param array $params 绑定参数
     * @return bool
     */
    public function sendExecute($stmtname, array $params)
    {
        return pg_send_execute($this->connection, $stmtname, $params);
    }

    /**
     * 发送一个请求，使用给定的参数创建一个准备好的语句，而不需要等待完成
     * @param string $stmtname 预处理语句名称
     * @param string $query SQL语句
     * @return bool
     */
    public function sendPrepare($stmtname, $query)
    {
        return pg_send_prepare($this->connection, $stmtname, $query);
    }

    /**
     * 在不等待结果的情况下向服务器提交命令和单独的参数
     * @param string $query SQL语句
     * @param array $params 绑定参数
     * @return bool
     */
    public function sendQueryParams($query, array $params)
    {
        return pg_send_query_params($this->connection, $query, $params);
    }

    /**
     * 发送异步查询
     * @param string $query SQL语句
     * @return bool
     */
    public function sendQuery($query)
    {
        return pg_send_query($this->connection, $query);
    }

    /**
     * 设定客户端编码
     * @param string $encoding 编码
     * @return int
     */
    public function setClientEncoding($encoding)
    {
        return pg_set_client_encoding($this->connection, $encoding);
    }

    /**
     * 确定消息的冗长
     * @param int $verbosity 冗长
     * @return int
     */
    public function setErrorVerbosity($verbosity)
    {
        return pg_set_error_verbosity($this->connection, $verbosity);
    }

    /**
     * 获取PostgreSQL连接下套接字的只读句柄
     * @return resource
     */
    public function socket()
    {
        return pg_socket($this->connection);
    }

    /**
     * 启动一个 PostgreSQL 连接的追踪功能
     * @param string $pathname 记录到 pathname 指定的文件中
     * @param string $mode 模式
     * @return bool
     */
    public function trace($pathname, $mode = "w")
    {
        return pg_trace($pathname, $mode, $this->connection);
    }

    /**
     * 返回服务器的当前事务内状态
     * @return int
     */
    public function transactionStatus()
    {
        return pg_transaction_status($this->connection);
    }

    /**
     * 返回该连接的 tty 号
     * @return string
     */
    public function tty()
    {
        return pg_tty($this->connection);
    }

    /**
     * 取消 bytea 类型中的字符串转义
     * @param string $data 字符串
     * @return string
     */
    public static function unescapeBytea($data)
    {
        return pg_unescape_bytea($data);
    }

    /**
     * 关闭 PostgreSQL 连接的追踪功能
     */
    public function untrace()
    {
        pg_untrace($this->connection);
    }

    /**
     * 更新表
     * @param string $table_name 表名
     * @param array $data 数据
     * @param array $condition 条件
     * @param int $options 选项常量
     */
    public function update($table_name, array $data, array $condition, $options = 512)
    {
        pg_update($this->connection, $table_name, $data, $condition, $options);
    }

    /**
     * 返回一个包含客户端、协议和服务器版本的数组(如果可用)
     * @return array
     */
    public function version()
    {
        return pg_version($this->connection);
    }

}
