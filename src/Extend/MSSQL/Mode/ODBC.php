<?php

namespace Fize\Database\Extend\MSSQL\Mode;

use Exception;
use Fize\Database\Extend\MSSQL\Db;
use Fize\Database\Middleware\ODBC as Middleware;

/**
 * ODBC
 *
 * ODBC模式MSSQL数据库模型类
 */
class ODBC extends Db
{
    use Middleware;

    /**
     * 构造
     * @param string      $host   服务器地址
     * @param string      $user   用户名
     * @param string      $pwd    用户密码
     * @param string      $dbname 数据库名
     * @param mixed       $port   端口号，选填，MSQL默认是1433
     * @param string|null $driver 指定ODBC驱动名称。
     * @throws Exception
     */
    public function __construct(string $host, string $user, string $pwd, string $dbname, $port = "", string $driver = null)
    {
        if (is_null($driver)) {
            //$driver = "{SQL Server}";  最低兼容
            $driver = "{SQL Server Native Client 11.0}";
        }
        if (empty($port)) {
            $server = $host;
        } else {
            $server = "$host,$port";
        }
        $dsn = "Driver=$driver;Server=$server;Database=$dbname";
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
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持原生的ODBC问号预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query(string $sql, array $params = [], callable $callback = null): array
    {
        $sql = iconv('UTF-8', 'GBK', $sql);
        array_walk($params, function (&$value) {
            $value = iconv('UTF-8', 'GBK', $value);
        });

        $result = $this->driver->prepare($sql);
        $result->execute($params);

        $rows = [];
        while ($row = $result->fetchArray()) {
            array_walk($row, function (&$value) {
                if (is_string($value)) {
                    $value = iconv('GBK', 'UTF-8', $value);
                }
            });
            if ($callback !== null) {
                $callback($row);
            }
            $rows[] = $row;
        }
        $result->freeResult();
        return $rows;
    }

    /**
     * 执行一个SQL语句
     * @param string $sql    SQL语句，支持问号预处理语句
     * @param array  $params 可选的绑定参数
     * @return int 返回受影响行数
     */
    public function execute(string $sql, array $params = []): int
    {
        $sql = iconv('UTF-8', 'GBK', $sql);
        array_walk($params, function (&$value) {
            $value = iconv('UTF-8', 'GBK', $value);
        });

        $result = $this->driver->prepare($sql);
        $result->execute($params);
        return $result->numRows();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在mssql中无效
     * @return int|string
     */
    public function lastInsertId(string $name = null)
    {
        $result = $this->driver->exec("SELECT @@IDENTITY");
        return $result->result(1);
    }
}
