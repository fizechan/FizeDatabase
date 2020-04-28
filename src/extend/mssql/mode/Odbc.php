<?php

namespace fize\db\extend\mssql\mode;

use Exception;
use fize\db\extend\mssql\Db;
use fize\db\middleware\Odbc as Middleware;

/**
 * ODBC
 *
 * ODBC模式MSSQL数据库模型类
 */
class Odbc extends Db
{
    use Middleware;

    /**
     * 构造
     * @param string $host   服务器地址
     * @param string $user   用户名
     * @param string $pwd    用户密码
     * @param string $dbname 数据库名
     * @param mixed  $port   端口号，选填，MSQL默认是1433
     * @param string $driver 指定ODBC驱动名称。
     * @throws Exception
     */
    public function __construct($host, $user, $pwd, $dbname, $port = "", $driver = null)
    {
        if (is_null($driver)) {
            //$driver = "{SQL Server}";  最低兼容
            $driver = "{SQL Server Native Client 11.0}";
        }
        if (empty($port)) {
            $server = $host;
        } else {
            $server = "{$host},{$port}";
        }
        $dsn = "Driver={$driver};Server={$server};Database={$dbname}";
        $this->odbcConstruct($dsn, $user, $pwd);
    }

    /**
     * 析构时释放ODBC资源
     */
    public function __destruct()
    {
        $this->odbcDestruct();
        parent::__destruct();
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string   $sql      SQL语句，支持原生的ODBC问号预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = iconv('UTF-8', 'GBK', $sql);
        array_walk($params, function (&$value) {
            $value = iconv('UTF-8', 'GBK', $value);
        });

        $result = $this->driver->prepare($sql);
        $result->execute($params);
        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($assoc = $result->fetchArray()) {
                    array_walk($assoc, function (&$value) {
                        if (is_string($value)) {
                            $value = iconv('GBK', 'UTF-8', $value);
                        }
                    });
                    $callback($assoc);
                }
                $result->freeResult();
                return null;
            } else {
                $rows = [];
                while ($row = $result->fetchArray()) {
                    array_walk($row, function (&$value) {
                        if (is_string($value)) {
                            $value = iconv('GBK', 'UTF-8', $value);
                        }
                    });
                    $rows[] = $row;
                }
                $result->freeResult();
                return $rows;
            }
        } else {
            return $result->numRows();
        }
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在mssql中无效
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $result = $this->driver->exec("SELECT @@IDENTITY");
        return $result->result(1);
    }
}
