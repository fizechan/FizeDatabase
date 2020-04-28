<?php

namespace fize\db\extend\mssql\mode\driver;

use Exception;
use fize\db\extend\mssql\mode\driver\sqlsrv\Statement;

/**
 * Sqlsrv
 *
 * MSSQL的官方支持方法对应类
 */
class Sqlsrv
{

    /**
     * @var resource 当前数据库链接对象
     */
    private $conn;

    /**
     * MSSQL constructor.
     * @param string $serverName     服务器,它可以包含一个端口号，则写法如{name},{port}
     * @param array  $connectionInfo 其他信息
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
     * @param mixed  $value   设置值。可能的值参照PHP手册
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public static function configure($setting, $value)
    {
        return sqlsrv_configure($setting, $value);
    }

    /**
     * 创建一个数据库连接
     * @param string $serverName     服务器名
     * @param array  $connectionInfo 设置项
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
     * 返回指定配置设置的值。
     * @param string $setting 设置名
     * @return mixed
     */
    public static function getConfig($setting)
    {
        return sqlsrv_get_config($setting);
    }

    /**
     * 设置一个预处理语句
     * @param string $sql     预处理SQL语句，支持问号占位符。
     * @param array  $params  可选的绑定参数
     * @param array  $options 其他相关参数。
     * @return Statement 使用该对象来进行实际查询
     */
    public function prepare($sql, array $params = null, array $options = null)
    {
        if (is_null($options)) {
            $stmt = sqlsrv_prepare($this->conn, $sql, $params);
        } else {
            $stmt = sqlsrv_prepare($this->conn, $sql, $params, $options);
        }
        return new Statement($stmt);
    }

    /**
     * 设置一个预处理语句，并执行。
     * @param string $sql     预处理SQL语句，支持问号占位符。
     * @param array  $params  可选的绑定参数
     * @param array  $options 其他相关参数。
     * @return Statement 使用该对象来进行实际查询
     */
    public function query($sql, array $params = null, array $options = null)
    {
        if (is_null($options)) {
            $stmt = sqlsrv_query($this->conn, $sql, $params);
        } else {
            $stmt = sqlsrv_query($this->conn, $sql, $params, $options);
        }
        return new Statement($stmt);
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
     * 获取数据库服务器信息。
     * @return array
     */
    public function serverInfo()
    {
        return sqlsrv_server_info($this->conn);
    }
}
