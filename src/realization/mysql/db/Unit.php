<?php


namespace fize\db\realization\mysql\db;

use fize\db\realization\mysql\Query;


/**
 * mysql数据库语句构造基本功能
 */
trait Unit
{
    /**
     * LIMIT语句
     * @var string
     */
    protected $_limit = "";

    /**
     * 本次查询是否启用LOCK锁
     * @var bool
     */
    protected $_lock = false;

    /**
     * LOCK语句主体
     * @var string
     */
    protected $_lock_sql = "";

    /**
     * 设置LIMIT,支持链式调用
     * @param int $rows 要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    public function limit($rows, $offset = null)
    {
        if (is_null($offset)) {
            $this->_limit = (string)$rows;
        } else {
            $this->_limit = (string)$offset . "," . (string)$rows;
        }
        return $this;
    }

    /**
     * 指定查询lock
     * @todo 写法不是很好，需要改进
     * @param bool $lock 是否启用LOCK语句
     * @param array $lock_sqls 表锁定语句快，支持多个，默认为启用当前表的写锁定
     * @return $this
     */
    public function lock($lock = true, array $lock_sqls = null)
    {
        $this->_lock = $lock;
        if ($this->_lock) {
            if (is_null($lock_sqls)) {
                $lock_sqls = ["{$this->_table_($this->_tablePrefix. $this->_tableName)}` WRITE"];
            }
            $this->_lock_sql = implode(", ", $lock_sqls);
        } else {
            $this->_lock_sql = "";
        }
        return $this;
    }

    /**
     * 设置WHERE语句,支持链式调用
     * @param mixed $statements “Query对象”或者“查询数组”或者“WHERE子语句”，其中“WHERE子语句”支持原生的PDO问号预处理占位符;
     * @param array $parse 如果$statements是SQL预处理语句，则可以传递本参数用于预处理替换参数数组
     * @return $this
     */
    public function where($statements, array $parse = [])
    {
        if (is_array($statements)) {  // 通常情况下，我们使用简洁方式来更简便地定义条件，对于复杂条件无法满足的，可以使用查询器或者直接使用预处理语句
            $query = new Query();
            $query->analyze($statements);
            $this->_where = $query->sql();
            $this->_whereParams = $query->params();
        } else {
            parent::where($statements, $parse);
        }
        return $this;
    }

    /**
     * HAVING语句，支持链式调用
     * @param mixed $statements “Query对象”或者“查询数组”或者“WHERE子语句”，其中“WHERE子语句”支持原生的PDO问号预处理占位符;
     * @param array $parse 如果$statements是SQL预处理语句，则可以传递本参数用于预处理替换参数数组
     * @return $this
     */
    public function having($statements, array $parse = [])
    {
        if (is_array($statements)) {  // 通常情况下，我们使用简洁方式来更简便地定义条件，对于复杂条件无法满足的，可以使用查询器或者直接使用预处理语句
            $query = new Query();
            $query->analyze($statements);
            $this->_having = $query->sql();
            $this->_havingParams = $query->params();
        } else {
            parent::having($statements, $parse);
        }
        return $this;
    }
}