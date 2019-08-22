<?php

namespace fize\db\middleware\adodb;


use COM;

/**
 * ADODB方式数据库trait中间层
 * 需要启用COM扩展
 */
trait Middleware
{

    /**
     * @var COM 连接对象
     */
    private $_conn;

    /**
     * @var int 编码
     */
    private $_codepage;

    /**
     * 构造Adodb
     * @param string $dsn DSN字符串
     * @param int $codepage 编码
     */
    protected function construct($dsn, $codepage)
    {
        $this->_codepage = $codepage;
        $this->_conn = new COM("ADODB.Connection", null, $codepage);
        $this->_conn->Open($dsn);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->_conn->Close();
        $this->_conn = null;
    }

    /**
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return COM
     */
    public function prototype()
    {
        return $this->_conn;
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
     * @param string $sql SQL语句，支持模拟问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组(错误返回false)，INSERT/REPLACE返回0，其余返回受影响行数
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = $this->getRealSql($sql, $params);
        if (stripos($sql, "INSERT") === 0) {
            $rst = $this->_conn->Execute($sql);
            if (!$rst) {
                return false;
            }
            return 0;
        } elseif (stripos($sql, "SELECT") === 0) {
            $rows = [];
            $rs = new COM("ADODB.RecordSet", null, $this->_codepage);
            $rs->Open($sql, $this->_conn, 1, 3);
            while(!$rs->Eof){
                $row = [];
                foreach ($rs->Fields as $field) {
                    $row[$field->Name] = $field->Value;
                }
                $rows[] = $row;
                $rs->MoveNext();
            }
            $rs->Close();
            if ($callback !== null) {
                foreach ($rows as $row) {
                    $callback($row);
                }
                return null;
            } else {
                return $rows;
            }
        } else {
            $ra = 0;
            $rst = $this->_conn->Execute($sql, $ra);
            if (!$rst) {
                return false;
            }
            return $ra;
        }
    }

    /**
     * 开始事务
     */
    public function startTrans()
    {
        $this->_conn->BeginTrans();
    }

    /**
     * 执行事务
     */
    public function commit()
    {
        $this->_conn->CommitTrans();
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->_conn->RollbackTrans();
    }
}