<?php

namespace fize\database\middleware;

use COM;

/**
 * ADODB
 */
trait Adodb
{

    /**
     * @var COM 连接对象
     */
    private $conn;

    /**
     * @var int 编码
     */
    private $codepage;

    /**
     * 构造Adodb
     * @see https://www.connectionstrings.com/
     * @param string $dsn      DSN字符串
     * @param int    $codepage 编码
     */
    protected function adodbConstruct($dsn, $codepage)
    {
        $this->codepage = $codepage;
        $this->conn = new COM("ADODB.Connection", null, $codepage);
        $this->conn->Open($dsn);
    }

    /**
     * 析构函数
     */
    protected function adodbDestruct()
    {
        $this->conn->Close();
        $this->conn = null;
    }

    /**
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return COM
     */
    public function prototype()
    {
        return $this->conn;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string   $sql      SQL语句，支持模拟问号占位符预处理语句
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = $this->getRealSql($sql, $params);
        if (stripos($sql, "SELECT") === 0) {
            $rows = [];
            $rs = new COM("ADODB.RecordSet", null, $this->codepage);
            $rs->Open($sql, $this->conn, 1, 3);
            while (!$rs->Eof) {
                $row = [];
                foreach ($rs->Fields as $field) {
                    $row[$field->Name] = (string)$field->Value;
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
            $rst = $this->conn->Execute($sql, $ra);
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
        $this->conn->BeginTrans();
    }

    /**
     * 执行事务
     */
    public function commit()
    {
        $this->conn->CommitTrans();
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->conn->RollbackTrans();
    }
}
