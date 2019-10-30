<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace fize\db\realization\access\mode;


use fize\db\realization\access\Db;
use fize\db\middleware\Odbc as Middleware;

/**
 * ODBC方式Access数据库模型类
 */
class Odbc extends Db
{
    use Middleware {
        Middleware::query as protected queryOdbc;
    }

    /**
     * 构造
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $driver 指定ODBC驱动名称。
     */
    public function __construct($file, $pwd = null, $driver = null)
    {
        if (is_null($driver)) {  //默认驱动名
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
     * 根据SQL预处理语句和绑定参数，返回实际的SQL
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @return string
     */
    private function getRealSql($sql, array $params = [])
    {
        if (!$params) {
            return $sql;
        }
        $temp = explode('?', $sql);
        $last_sql = "";
        for ($i = 0; $i < count($temp) - 1; $i++) {
            $last_sql .= $temp[$i] . $this->parseValue($params[$i]);
        }
        $last_sql .= $temp[count($temp) - 1];
        return $last_sql;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持模拟的问号预处理语句
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = $this->getRealSql($sql, $params);
        $sql = iconv('UTF-8', 'GBK', $sql);
        $result = $this->driver->exec($sql);  //ACCESS不支持prepare，故使用exec方法
        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($row = $result->fetchArray()) {
                    array_walk($assoc, function (&$value) {
                        $value = iconv('GBK', 'UTF-8', $value);
                    });
                    $callback($row);
                }
                $result->freeResult();
                return null;
            } else {
                $rows = [];
                while ($row = $result->fetchArray()) {
                    array_walk($row, function (&$value) {
                        $value = iconv('GBK', 'UTF-8', $value);
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
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在access中无效
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $result = $this->driver->exec("SELECT @@IDENTITY");
        return $result->result(1);
    }
}