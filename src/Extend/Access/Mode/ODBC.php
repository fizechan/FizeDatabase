<?php

namespace Fize\Database\Extend\Access\Mode;

use Fize\Database\Extend\Access\Db;
use Fize\Database\Middleware\ODBC as Middleware;

/**
 * ODBC
 *
 * ODBC方式Access数据库模型
 */
class ODBC extends Db
{
    use Middleware;

    /**
     * 构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     */
    public function __construct(string $file, string $pwd = null, string $driver = null)
    {
        if (is_null($driver)) {
            $driver = "Microsoft Access Driver (*.mdb, *.accdb)";
        }
        $dsn = "Driver={" . $driver . "};DSN='';DBQ=" . realpath($file) . ";";
        $this->odbcConstruct($dsn, '', $pwd);
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
     * @param string   $sql      SQL语句，支持模拟的问号预处理语句
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query(string $sql, array $params = [], callable $callback = null): array
    {
        $sql = $this->getRealSql($sql, $params);
        $sql = iconv('UTF-8', 'GBK', $sql);
        $result = $this->driver->exec($sql);  //ACCESS不支持prepare，故使用exec方法
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
        $sql = $this->getRealSql($sql, $params);
        $sql = iconv('UTF-8', 'GBK', $sql);
        $result = $this->driver->exec($sql);  //ACCESS不支持prepare，故使用exec方法
        return $result->numRows();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在access中无效
     * @return int|string
     */
    public function lastInsertId(string $name = null)
    {
        $result = $this->driver->exec("SELECT @@IDENTITY");
        return $result->result(1);
    }
}
