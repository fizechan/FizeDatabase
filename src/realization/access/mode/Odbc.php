<?php

namespace fize\db\realization\access\mode;


use fize\db\realization\access\Db;
use fize\db\middleware\odbc\Middleware;

/**
 * ODBC方式Access数据库模型类
 * 注意ODBC返回的类型都为字符串格式(null除外)
 */
class Odbc extends Db
{
    use Middleware {
        Middleware::query as protected queryOdbc;
    }

    /**
     * 构造
     * @param string $db_file Access文件路径
     * @param string $pwd 用户密码
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param string $driver 指定ODBC驱动名称。
     */
    public function __construct($db_file, $pwd = null, $prefix = "", $driver = null)
    {
        $this->_tablePrefix = $prefix;
        if (is_null($driver)) {  //默认驱动名
            $driver = "{Microsoft Access Driver (*.mdb, *.accdb)}";
        }
        $dsn = "Driver={$driver};DSN='';DBQ=" . realpath($db_file) . ";";
        $this->construct($dsn, '', $pwd);
    }

    /**
     * 自己实现的安全化值
     * @param mixed $value 要安全化的值
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . str_replace("'", "''", $value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'NULL';
        }
        return $value;
    }

    /**
     * 对中文兼容性处理
     * @param string $string 待处理字符串
     * @param string $direction 方向 UTF8_2_GBK,GBK_2_UTF8
     * @return string 处理后字符串
     */
    private static function stringSerialize($string, $direction)
    {
        if ($direction == 'UTF8_2_GBK') {
            $string = iconv('UTF-8', 'GBK', $string);
        } else if ($direction == 'GBK_2_UTF8') {
            $string = iconv('GBK', 'UTF-8', $string);
        }
        return $string;
    }

    /**
     * 根据SQL预处理语句和绑定参数，返回实际的SQL
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @return string
     */
    private function getRealSql($sql, array $params = [])
    {
        if(!$params) {
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
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = $this->getRealSql($sql, $params);
        $sql = self::stringSerialize($sql, 'UTF8_2_GBK');
        $this->_driver->exec($sql);  //ACCESS不支持prepare，故使用exec方法
        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($row = $this->_driver->fetchArray()) {
                    array_walk($assoc, function(&$value){
                        $value = self::stringSerialize($value, 'GBK_2_UTF8');
                    });
                    $callback($row);
                }
                $this->_driver->freeResult();
                return null;
            } else {
                $rows = [];
                while ($row = $this->_driver->fetchArray()) {
                    array_walk($row, function(&$value){
                        $value = self::stringSerialize($value, 'GBK_2_UTF8');
                    });
                    $rows[] = $row;
                }
                $this->_driver->freeResult();
                return $rows; //返回数组
            }
        } else if(stripos($sql, "INSERT") === 0){
            $this->_driver->exec("SELECT @@IDENTITY");
            $id = $this->_driver->result(1);
            return $id; //返回自增ID
        }else{
            $num = $this->_driver->numRows();
            return $num;
        }
    }
}