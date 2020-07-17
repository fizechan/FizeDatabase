<?php

namespace fize\database\middleware;

use COM;
use fize\database\exception\Exception;

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
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持模拟问号占位符预处理语句
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = $this->getRealSql($sql, $params);
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
        }
        return $rows;
    }

    /**
     * 执行一个SQL语句
     * @param string $sql    SQL语句，支持问号预处理语句
     * @param array  $params 可选的绑定参数
     * @return int 返回受影响行数
     */
    public function execute($sql, array $params = [])
    {
        $sql = $this->getRealSql($sql, $params);
        $ra = 0;
        $rst = $this->conn->Execute($sql, $ra);
        if (!$rst) {
            throw new Exception('执行SQL语句时发生错误', 0, $sql);
        }
        return $ra;
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
