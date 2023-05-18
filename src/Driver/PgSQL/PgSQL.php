<?php

namespace Fize\Database\Driver\PgSQL;

/**
 * PostgreSQL驱动
 */
class PgSQL
{

    /**
     * @var resource 连接
     */
    protected $connection = null;

    /**
     * 构造时创建连接
     * @param string   $connection_string 连接字符串
     * @param bool     $pconnect          是否使用长连接
     * @param int|null $connect_type      PGSQL_CONNECT_FORCE_NEW使用新连接
     */
    public function __construct(string $connection_string, bool $pconnect = false, int $connect_type = null)
    {
        if ($pconnect) {
            $this->pconnect($connection_string, $connect_type);
        }
        $this->connect($connection_string);
    }

    /**
     * 析构时关闭连接
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 取消异步查询
     * @return bool
     */
    public function cancelQuery(): bool
    {
        return pg_cancel_query($this->connection);
    }

    /**
     * 取得客户端编码方式
     * @return string
     */
    public function clientEncoding(): string
    {
        return pg_client_encoding($this->connection);
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        if ($this->connection) {
            pg_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * 正在进行尝试轮询 PostgreSQL 链接状态
     * @return int 返回常量 PGSQL_POLLING_FAILED, PGSQL_POLLING_READING, PGSQL_POLLING_WRITING, PGSQL_POLLING_OK, 或者 PGSQL_POLLING_ACTIVE
     */
    public function connectPoll(): int
    {
        return pg_connect_poll($this->connection);
    }

    /**
     * 打开一个 PostgreSQL 连接
     * @param string $connection_string 连接字符串
     */
    public function connect(string $connection_string)
    {
        $this->close();
        $this->connection = pg_connect($connection_string);
    }

    /**
     * 获知连接是否为忙
     * @return bool
     */
    public function connectionBusy(): bool
    {
        return pg_connection_busy($this->connection);
    }

    /**
     * 重置连接（再次连接）
     * @return bool
     */
    public function connectionReset(): bool
    {
        return pg_connection_reset($this->connection);
    }

    /**
     * 获得连接状态
     * @return int 可能的状态为 PGSQL_CONNECTION_OK 和 PGSQL_CONNECTION_BAD
     */
    public function connectionStatus(): int
    {
        return pg_connection_status($this->connection);
    }

    /**
     * 读取连接上的输入
     * @return bool
     */
    public function consumeInput(): bool
    {
        return pg_consume_input($this->connection);
    }

    /**
     * 将关联的数组值转换为适合 SQL 语句的格式
     * @notice 此函数是实验性的
     * @param string $table_name  表名
     * @param array  $assoc_array 键值对
     * @param int    $options     常量PGSQL_CONV_IGNORE_DEFAULT, PGSQL_CONV_FORCE_NULL, PGSQL_CONV_IGNORE_NOT_NULL
     * @return array
     */
    public function convert(string $table_name, array $assoc_array, int $options = 0): array
    {
        return pg_convert($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 根据数组将记录插入表中
     * @param string      $table_name 表名
     * @param array       $rows       要插入的记录
     * @param string|null $delimiter  间隔符
     * @param string|null $null_as    NULL值的替代
     * @return bool
     */
    public function copyFrom(string $table_name, array $rows, string $delimiter = null, string $null_as = null): bool
    {
        if (!is_null($null_as)) {
            return pg_copy_from($this->connection, $table_name, $rows, $delimiter, $null_as);
        }
        if (!is_null($delimiter)) {
            return pg_copy_from($this->connection, $table_name, $rows, $delimiter);
        }
        return pg_copy_from($this->connection, $table_name, $rows);
    }

    /**
     * 将一个表拷贝到数组中
     * @param string      $table_name 表名
     * @param string|null $delimiter  间隔符
     * @param string|null $null_as    NULL值的替代
     * @return array
     */
    public function copyTo(string $table_name, string $delimiter = null, string $null_as = null): array
    {
        if (!is_null($null_as)) {
            return pg_copy_to($this->connection, $table_name, $delimiter, $null_as);
        }
        if (!is_null($delimiter)) {
            return pg_copy_to($this->connection, $table_name, $delimiter);
        }
        return pg_copy_to($this->connection, $table_name);
    }

    /**
     * 获得数据库名
     * @return string
     */
    public function dbname(): string
    {
        return pg_dbname($this->connection);
    }

    /**
     * 删除记录
     * @notice 此函数是实验性的
     * @param string $table_name  表名
     * @param array  $assoc_array 以 field=>value 格式给出的条件
     * @param int    $options     常量PGSQL_CONV_FORCE_NULL, PGSQL_DML_NO_CONV, PGSQL_DML_EXEC or PGSQL_DML_STRING
     * @return bool|string 选项带PGSQL_DML_STRING时返回SQL语句，其他情况返回bool
     */
    public function delete(string $table_name, array $assoc_array, int $options = 512)
    {
        return pg_delete($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 与 PostgreSQL 后端同步
     * @return bool
     */
    public function endCopy(): bool
    {
        return pg_end_copy($this->connection);
    }

    /**
     * 转义 bytea 类型的二进制数据
     * @param string $data 二进制字符串
     * @return string
     */
    public function escapeBytea(string $data): string
    {
        return pg_escape_bytea($this->connection, $data);
    }

    /**
     * 转义用于插入文本字段的标识符
     * @param string $data 文本
     * @return string
     */
    public function escapeIdentifier(string $data): string
    {
        return pg_escape_identifier($this->connection, $data);
    }

    /**
     * 转义用于插入文本字段的文字
     * @param string $data 文本
     * @return string
     */
    public function escapeLiteral(string $data): string
    {
        return pg_escape_literal($this->connection, $data);
    }

    /**
     * 转义 text/char 类型的字符串
     * @param string $data 文本
     * @return string
     */
    public function escapeString(string $data): string
    {
        return pg_escape_string($this->connection, $data);
    }

    /**
     * 发送一个请求来执行带有给定参数的准备好的语句，并等待结果
     * @param string $stmtname SQL预处理语句
     * @param array  $params   绑定参数
     * @return Result|false Result对象来进行数据集操作，失败时返回false
     */
    public function execute(string $stmtname, array $params)
    {
        $result = pg_execute($this->connection, $stmtname, $params);
        if ($result === false) {
            return false;
        }
        return new Result($result);
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
     * Ping 数据库连接
     * @param int|null $result_type 返回类型
     * @return array 通告消息
     */
    public function getNotify(int $result_type = null): array
    {
        if (!is_null($result_type)) {
            return pg_get_notify($this->connection, $result_type);
        }
        return pg_get_notify($this->connection);
    }

    /**
     * 取得后端（数据库服务器进程）的 PID
     * @return int
     */
    public function getPid(): int
    {
        return pg_get_pid($this->connection);
    }

    /**
     * 取得异步查询结果
     * @return Result|false Result对象来进行数据集操作，失败时返回false
     */
    public function getResult()
    {
        $result = pg_get_result($this->connection);
        if ($result === false) {
            return false;
        }
        return new Result($result);
    }

    /**
     * 返回和某连接关联的主机名
     * @return string
     */
    public function host(): string
    {
        return pg_host($this->connection);
    }

    /**
     * 将数组插入到表中
     * @param string $table_name  表名
     * @param array  $assoc_array 数组
     * @param int    $options     常量PGSQL_CONV_OPTS, PGSQL_DML_NO_CONV, PGSQL_DML_EXEC, PGSQL_DML_ASYNC or PGSQL_DML_STRING
     * @return mixed
     */
    public function insert(string $table_name, array $assoc_array, int $options = 512)
    {
        return pg_insert($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 得到某连接的最后一条错误信息
     * @return string
     */
    public function lastError(): string
    {
        return pg_last_error($this->connection);
    }

    /**
     * 返回 PostgreSQL 服务器最新一条公告信息
     * @return string
     */
    public function lastNotice(): string
    {
        return pg_last_notice($this->connection);
    }

    /**
     * 新建一个大型对象
     * @return int
     */
    public function loCreate(): int
    {
        return pg_lo_create($this->connection);
    }

    /**
     * 将大型对象导出到文件
     * @param int    $oid      要导出的数据库里的大型对象的 OID
     * @param string $pathname 要导出的数据库里的大型对象的文件在客户端上完整路径和文件名
     * @return bool
     */
    public function loExport(int $oid, string $pathname): bool
    {
        return pg_lo_export($this->connection, $oid, $pathname);
    }

    /**
     * 将文件导入为大型对象
     * @param string $pathname  变量指明了要导入为大型对象的文件名
     * @param mixed  $object_id 尝试用该对象ID创建
     * @return int
     */
    public function loImport(string $pathname, $object_id = null): int
    {
        if ($object_id) {
            return pg_lo_import($this->connection, $pathname, $object_id);
        }
        return pg_lo_import($this->connection, $pathname);
    }

    /**
     * 打开一个大型对象
     * @param int    $oid  指定了有效的大型对象的 oid
     * @param string $mode 可以为 "r"，"w" 或者 "rw"。
     * @return LO|false 失败则返回 FALSE
     */
    public function loOpen(int $oid, string $mode)
    {
        $large_object = pg_lo_open($this->connection, $oid, $mode);
        if ($large_object === false) {
            return false;
        }
        return new LO($large_object);
    }

    /**
     * 删除一个大型对象
     * @param int $oid 对象ID
     * @return bool
     */
    public function loUnlink(int $oid): bool
    {
        return pg_lo_unlink($this->connection, $oid);
    }

    /**
     * 获得表的元数据
     * @notice 此函数是实验性的
     * @param string $table_name 表名
     * @return array
     */
    public function metaData(string $table_name): array
    {
        return pg_meta_data($this->connection, $table_name);
    }

    /**
     * 获得和连接有关的选项
     * @return string
     */
    public function options(): string
    {
        return pg_options($this->connection);
    }

    /**
     * 查找服务器的当前参数设置
     * @param string $param_name 参数名
     * @return string
     */
    public function parameterStatus(string $param_name): string
    {
        return pg_parameter_status($this->connection, $param_name);
    }

    /**
     * 打开一个持久的 PostgreSQL 连接
     * @param string   $connection_string 连接字符串
     * @param int|null $connect_type      PGSQL_CONNECT_FORCE_NEW强制新连接
     */
    protected function pconnect(string $connection_string, int $connect_type = null)
    {
        $this->connection = pg_pconnect($connection_string, $connect_type);
    }

    /**
     * Ping 数据库连接
     * @return bool
     */
    public function ping(): bool
    {
        return pg_ping($this->connection);
    }

    /**
     * 返回该连接的端口号
     * @return int
     */
    public function port(): int
    {
        return pg_port($this->connection);
    }

    /**
     * 提交一个请求，用给定的参数创建一个准备好的语句，并等待完成
     * @param string $stmtname 名称
     * @param string $query    语句
     * @return resource
     */
    public function prepare(string $stmtname, string $query)
    {
        return pg_prepare($this->connection, $stmtname, $query);
    }

    /**
     * 向 PostgreSQL 后端发送以 NULL 结尾的字符串
     * @param string $data 数据
     * @return bool
     */
    public function putLine(string $data): bool
    {
        return pg_put_line($this->connection, $data);
    }

    /**
     * 向服务器提交一个命令并等待结果，同时能够独立于SQL命令文本传递参数
     * @param string $query  SQL语句，支持占位符
     * @param array  $params 绑定参数
     * @return Result|false 失败时返回false
     */
    public function queryParams(string $query, array $params)
    {
        $result = pg_query_params($this->connection, $query, $params);
        if ($result === false) {
            return false;
        }
        return new Result($result);
    }

    /**
     * 执行查询
     * @param string $query SQL语句
     * @return Result|false 失败时返回false
     */
    public function query(string $query)
    {
        $result = pg_query($this->connection, $query);
        if ($result === false) {
            return false;
        }
        return new Result($result);
    }

    /**
     * 选择记录
     * @notice 此函数是实验性的
     * @param string $table_name  表名
     * @param array  $assoc_array 条件数组
     * @param int    $options     选项常量
     * @return array|string 如果选项带PGSQL_DML_STRING则返回SQL语句，其他返回结果数组
     */
    public function select(string $table_name, array $assoc_array, int $options = 512)
    {
        return pg_select($this->connection, $table_name, $assoc_array, $options);
    }

    /**
     * 发送一个请求来执行带有给定参数的准备好的语句，而不需要等待结果
     * @param string $stmtname 预处理语句名称
     * @param array  $params   绑定参数
     * @return bool
     */
    public function sendExecute(string $stmtname, array $params): bool
    {
        return pg_send_execute($this->connection, $stmtname, $params);
    }

    /**
     * 发送一个请求，使用给定的参数创建一个准备好的语句，而不需要等待完成
     * @param string $stmtname 预处理语句名称
     * @param string $query    SQL语句
     * @return bool
     */
    public function sendPrepare(string $stmtname, string $query): bool
    {
        return pg_send_prepare($this->connection, $stmtname, $query);
    }

    /**
     * 在不等待结果的情况下向服务器提交命令和单独的参数
     * @param string $query  SQL语句
     * @param array  $params 绑定参数
     * @return bool
     */
    public function sendQueryParams(string $query, array $params): bool
    {
        return pg_send_query_params($this->connection, $query, $params);
    }

    /**
     * 发送异步查询
     * @param string $query SQL语句
     * @return bool
     */
    public function sendQuery(string $query): bool
    {
        return pg_send_query($this->connection, $query);
    }

    /**
     * 设定客户端编码
     * @param string $encoding 编码
     * @return int 成功返回 0，出错返回 -1
     */
    public function setClientEncoding(string $encoding): int
    {
        return pg_set_client_encoding($this->connection, $encoding);
    }

    /**
     * 确定消息的冗长
     * @param int $verbosity 冗长
     * @return int 常量PGSQL_ERRORS_TERSE, PGSQL_ERRORS_DEFAULT 或 PGSQL_ERRORS_VERBOSE
     */
    public function setErrorVerbosity(int $verbosity): int
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
     * @param string $mode     模式
     * @return bool
     */
    public function trace(string $pathname, string $mode = "w"): bool
    {
        return pg_trace($pathname, $mode, $this->connection);
    }

    /**
     * 返回服务器的当前事务内状态
     * @return int
     */
    public function transactionStatus(): int
    {
        return pg_transaction_status($this->connection);
    }

    /**
     * 返回该连接的 tty 号
     * @return string
     */
    public function tty(): string
    {
        return pg_tty($this->connection);
    }

    /**
     * 取消 bytea 类型中的字符串转义
     * @param string $data 字符串
     * @return string
     */
    public static function unescapeBytea(string $data): string
    {
        return pg_unescape_bytea($data);
    }

    /**
     * 关闭 PostgreSQL 连接的追踪功能
     * @return bool
     */
    public function untrace(): bool
    {
        return pg_untrace($this->connection);
    }

    /**
     * 更新表
     * @param string $table_name 表名
     * @param array  $data       数据
     * @param array  $condition  条件
     * @param int    $options    选项常量
     * @return bool|string 选项带PGSQL_DML_STRING时返回SQL语句，其他情况返回bool
     */
    public function update(string $table_name, array $data, array $condition, int $options = 512)
    {
        return pg_update($this->connection, $table_name, $data, $condition, $options);
    }

    /**
     * 返回一个包含客户端、协议和服务器版本的数组(如果可用)
     * @return array
     */
    public function version(): array
    {
        return pg_version($this->connection);
    }
}
