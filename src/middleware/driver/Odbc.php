<?php

namespace fize\db\middleware\driver;

use Exception;
use fize\db\exception\DriverException;
use fize\db\middleware\driver\odbc\Statement;


/**
 * ODBC驱动
 *
 * ODBC的SQL预处理语句对中文支持跟ODBC驱动有关，例如{MySQL ODBC 5.3 ANSI Driver}、{MySQL ODBC 5.3 Unicode Driver}。
 * 如果发现中文乱码问题，可以尝试替换驱动。
 */
class Odbc
{

    /**
     * @var resource 当前连接标识符
     */
    private $connection = null;

    /**
     * 构造
     * @see https://www.connectionstrings.com/ 可用DSN参见
     * @param string $dsn         连接的数据库源名称。另外，一个无DSN连接字符串可以使用。
     * @param string $user        用户名
     * @param string $pwd         密码
     * @param int    $cursor_type 可选SQL_CUR_USE_IF_NEEDED | SQL_CUR_USE_ODBC | SQL_CUR_USE_DRIVER
     * @param bool   $pconnect    是否使用长链接，默认false
     * @throws DriverException
     */
    public function __construct($dsn, $user, $pwd, $cursor_type = null, $pconnect = false)
    {
        try {
            if ($pconnect) {
                $this->connection = odbc_pconnect($dsn, $user, $pwd, $cursor_type);
            } else {
                $this->connection = odbc_connect($dsn, $user, $pwd, $cursor_type);
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $message = iconv('GB2312', 'UTF-8', $e->getMessage());
            throw new DriverException($message, $code);
        }
    }

    /**
     * 析构
     *
     * 由于调用closeAll时会关闭所有链接，导致影响close的执行，故析构时并没有将链接关闭，需要时，请手动关闭。
     */
    public function __destruct()
    {
    }

    /**
     * 获取或设置自动提交状态
     * @param bool $OnOff 当带$OnOff时，True表示开始自动提交，False表示关闭自动提交，null标识获取状态
     * @return int 返回状态时开启为非0值，关闭为0值，设置状态则返回结果
     * @throws DriverException
     */
    public function autocommit($OnOff = null)
    {
        if ($OnOff === null) {
            $rst = odbc_autocommit($this->connection);
        } else {
            $rst = odbc_autocommit($this->connection, $OnOff);
        }
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
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
     * @param string $qualifier   限定符。
     * @param string $owner       所有人，支持%(零个或多个字符)_(1个字符)
     * @param string $table_name  表名，支持%(零个或多个字符)_(1个字符)
     * @param string $column_name 列名，支持%(零个或多个字符)_(1个字符)
     * @return Statement
     * @throws DriverException
     * @todo 20170613测试，一直为空，不知为何。
     */
    public function columnprivileges($qualifier, $owner, $table_name, $column_name)
    {
        $rst = odbc_columnprivileges($this->connection, $qualifier, $owner, $table_name, $column_name);
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return new Statement($rst);
    }

    /**
     * 列出指定表中的列名。
     * @param string $qualifier   限定符。
     * @param string $schema      所有人，支持%(零个或多个字符)_(1个字符)
     * @param string $table_name  $table_name 表名，支持%(零个或多个字符)_(1个字符)
     * @param string $column_name 列名，支持%(零个或多个字符)_(1个字符)
     * @return Statement
     * @throws DriverException
     */
    public function columns($qualifier = null, $schema = null, $table_name = null, $column_name = null)
    {
        $rst = odbc_columns($this->connection, $qualifier, $schema, $table_name, $column_name);
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return new Statement($rst);
    }

    /**
     * 提交ODBC事务
     * @throws DriverException
     */
    public function commit()
    {
        $rst = odbc_commit($this->connection);
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
    }

    /**
     * 返回当前连接的信息。
     * @param int $fetch_type 可选SQL_FETCH_FIRST | SQL_FETCH_NEXT
     * @return array
     * @throws DriverException
     */
    public function dataSource($fetch_type)
    {
        $rst = odbc_data_source($this->connection, $fetch_type);
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return $rst;
    }

    /**
     * 执行一个SQL语句，返回结果集
     * @param string $query_string SQL语句
     * @param int    $flags        此参数目前没有使用
     * @return Statement
     * @throws DriverException
     */
    public function exec($query_string, $flags = null)
    {
        $rst = odbc_exec($this->connection, $query_string, $flags);
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return new Statement($rst);
    }

    /**
     * 获取检索外键的列表。
     * @param string $pk_qualifier 主键限定符。
     * @param string $pk_owner     主键所有者。
     * @param string $pk_table     主键表。
     * @param string $fk_qualifier 外键限定符。
     * @param string $fk_owner     外键所有者。
     * @param string $fk_table     外键表。
     * @return Statement 返回结果集
     * @throws DriverException
     */
    public function foreignkeys($pk_qualifier, $pk_owner, $pk_table, $fk_qualifier, $fk_owner, $fk_table)
    {
        $rst = odbc_foreignkeys($this->connection, $pk_qualifier, $pk_owner, $pk_table, $fk_qualifier, $fk_owner, $fk_table);
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return new Statement($rst);
    }

    /**
     * 检索有关数据源支持的数据类型的信息。
     * @param int $data_type 数据类型，可用于将信息限制为单个数据类型。
     * @return Statement 返回结果集，错误时返回false
     */
    public function gettypeinfo($data_type = null)
    {
        $rst = odbc_gettypeinfo($this->connection, $data_type);
        return new Statement($rst);
    }

    /**
     * 设置一个预处理语句
     * @param string $query_string 预处理语句，支持问号占位符
     * @return Statement 该返回值，可以使用execute()进行实际执行
     * @throws DriverException
     */
    public function prepare($query_string)
    {
        $rst = odbc_prepare($this->connection, $query_string);
        if ($rst === false) {
            throw new DriverException(odbc_error($this->connection), odbc_errormsg($this->connection));
        }
        return new Statement($rst);
    }

    /**
     * 获取指定表的主键
     * @param string $qualifier 限定符
     * @param string $owner     所有者
     * @param string $table     表名
     * @return Statement 结果集
     */
    public function primarykeys($qualifier, $owner, $table)
    {
        $rst = odbc_primarykeys($this->connection, $qualifier, $owner, $table);
        return new Statement($rst);
    }

    /**
     * 检索参数到过程的信息
     * @param string $qualifier 限定符
     * @param string $owner     所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $proc      过程。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $column    列名。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @return Statement 结果集
     * @deprecated 非常见用法，不建议使用
     */
    public function procedurecolumns($qualifier = null, $owner = null, $proc = null, $column = null)
    {
        $rst = odbc_procedurecolumns($this->connection, $qualifier, $owner, $proc, $column);
        return new Statement($rst);
    }

    /**
     * 获取存储在特定数据源中的过程列表。
     * @param string $qualifier 限定符
     * @param string $owner     所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $name      名称。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @return Statement 结果集
     * @deprecated 非常见用法，不建议使用
     */
    public function procedures($qualifier = null, $owner = null, $name = null)
    {
        $rst = odbc_procedures($this->connection, $qualifier, $owner, $name);
        return new Statement($rst);
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
     * @param int $option 属性名
     * @param int $param  属性值
     * @return bool
     */
    public function setoption($option, $param)
    {
        return odbc_setoption($this->connection, 1, $option, $param);
    }

    /**
     * 获取指定表的索引
     * @param int    $type      指定类型，可选SQL_BEST_ROWID | SQL_ROWVER特殊值
     * @param string $qualifier 限定符
     * @param string $owner     所有者
     * @param string $table     表名
     * @param int    $scope     命令结果集的作用域。
     * @param int    $nullable  null选项
     * @return Statement 结果集
     */
    public function specialcolumns($type, $qualifier, $owner, $table, $scope, $nullable)
    {
        $rst = odbc_specialcolumns($this->connection, $type, $qualifier, $owner, $table, $scope, $nullable);
        return new Statement($rst);
    }

    /**
     * 检索表的统计信息
     * @param string $qualifier  限定符
     * @param string $owner      所有者
     * @param string $table_name 表名
     * @param int    $unique     特有属性
     * @param int    $accuracy   准确性
     * @return Statement 结果集
     */
    public function statistics($qualifier, $owner, $table_name, $unique, $accuracy)
    {
        $rst = odbc_statistics($this->connection, $qualifier, $owner, $table_name, $unique, $accuracy);
        return new Statement($rst);
    }

    /**
     * 列出与每个表相关联的表和特权。
     * @param string $qualifier 限定符
     * @param string $owner     所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $name      名称。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @return Statement 结果集
     */
    public function tableprivileges($qualifier, $owner, $name)
    {
        $rst = odbc_tableprivileges($this->connection, $qualifier, $owner, $name);
        return new Statement($rst);
    }

    /**
     * 获取存储在特定数据源中的表名列表。
     * @param string $qualifier 限定符
     * @param string $owner     所有者。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $name      名称。此参数接受下列查询模式："%" 来匹配零到多个字符，"_" 来匹配单个字符。
     * @param string $types     指定类型，"'TABLE','VIEW'" or "TABLE, VIEW"
     * @return Statement 结果集
     */
    public function tables($qualifier = null, $owner = null, $name = null, $types = null)
    {
        $rst = odbc_tables($this->connection, $qualifier, $owner, $name, $types);
        return new Statement($rst);
    }
}
