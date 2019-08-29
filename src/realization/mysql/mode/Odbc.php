<?php

namespace fize\db\realization\mysql\mode;


use fize\db\realization\mysql\Db;
use fize\db\middleware\Odbc as Middleware;

/**
 * ODBC方式MySQL数据库模型类
 * 注意ODBC返回的类型都为字符串格式(null除外)
 * @package fize\db\realization\mysql\mode
 */
class Odbc extends Db
{
    use Middleware {
        Middleware::query as protected queryOdbc;  //使用别名来解决ODBC本身占用了query方法的问题
    }

    /**
     * 构造
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param mixed $port 端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param string $driver 指定ODBC驱动名称。
     */
    public function __construct($host, $user, $pwd, $dbname, $prefix = "", $port = "", $charset = "utf8", $driver = null)
    {
        $this->tablePrefix = $prefix;
        if (is_null($driver)) {  //默认驱动名
            $driver = "{MySQL ODBC 5.3 ANSI Driver}";
        }
        $dsn = "DRIVER={$driver};SERVER={$host};DATABASE={$dbname};CHARSET={$charset}";
        if (!empty($port)) {
            $dsn .= ";PORT={$port}";
        }
        $this->odbcConstruct($dsn, $user, $pwd);
    }

    /**
     * 析构时关闭ODBC
     */
    public function __destruct()
    {
        $this->odbcDestruct();
        parent::__destruct();
    }

    /**
     * 安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * ODBC为驱动层，安全化值应由各数据库自行实现
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . addcslashes($value, "'") . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int|null SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $result = $this->queryOdbc($sql, $params, $callback);
        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
            $this->driver->exec("SELECT @@IDENTITY");
            $id = $this->driver->result(1);
            return $id; //返回自增ID
        } elseif (stripos($sql, "SELECT") === 0) {
            return $result;
        } else {
            $this->driver->exec("SELECT ROW_COUNT()");
            $rows = $this->driver->result(1);
            return (int)$rows; //返回受影响条数
        }
    }
}