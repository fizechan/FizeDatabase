<?php

namespace fize\db\realization\pgsql;


use fize\db\definition\Db as Base;


/**
 * PostgreSQL的ORM模型
 */
abstract class Db extends Base
{
    use Feature;

    /**
     * LIMIT语句
     * @var string
     */
    protected $limit = "";

    /**
     * 本次查询是否启用LOCK锁
     * @var bool
     */
    protected $lock = false;

    /**
     * LOCK语句主体
     * @var string
     */
    protected $lock_sql = "";

    /**
     * 设置LIMIT,支持链式调用
     * @param int $rows 要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    public function limit($rows, $offset = null)
    {
        if (is_null($offset)) {
            $this->limit = (string)$rows;
        } else {
            $this->limit = (string)$offset . "," . (string)$rows;
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
        $this->lock = $lock;
        if ($this->lock) {
            if (is_null($lock_sqls)) {
                $lock_sqls = ["{$this->formatTable($this->tablePrefix. $this->tableName)}` WRITE"];
            }
            $this->lock_sql = implode(", ", $lock_sqls);
        } else {
            $this->lock_sql = "";
        }
        return $this;
    }

    /**
     * 清空当前条件，以便于下次查询
     */
    protected function clear()
    {
        parent::clear();
        $this->limit = "";
        $this->lock = false;
        $this->lock_sql = "";
    }

    /**
     * 根据当前条件构建SQL语句
     * @param string $action SQL语句类型
     * @param array $data 可能需要的数据
     * @param bool $clear 是否清理当前条件，默认true
     * @return string 最后组装的SQL语句
     */
    public function build($action, array $data = [], $clear = true)
    {
        if ($action == 'REPLACE') {
            $params = [];
            $sql = "REPLACE INTO {$this->formatTable($this->tablePrefix. $this->tableName)}{$this->parseInsertDatas($data, $params)}";
            $this->sql = $sql;
            $this->params = $params;
            return $sql; //REPLACE语句已完整
        } elseif ($action == 'TRUNCATE') {
            $sql = "TRUNCATE TABLE {$this->formatTable($this->tablePrefix . $this->tableName)}";
            $this->_sql = $sql;
            return $sql; //TRUNCATE语句已完整
        } else {
            $sql = parent::build($action, $data, false);
        }
        if (!empty($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
        }
        $this->sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }

    /**
     * CROSS JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function crossJoin($table, $on = null)
    {
        return $this->join($table, "CROSS JOIN", $on);
    }

    /**
     * LEFT OUTER JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function leftOuterJoin($table, $on = null)
    {
        return $this->join($table, "LEFT OUTER JOIN", $on);
    }

    /**
     * RIGHT OUTER JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function rightOuterJoin($table, $on = null)
    {
        return $this->join($table, "RIGHT OUTER JOIN", $on);
    }

    /**
     * STRAIGHT_JOIN条件，非标准SQL语句，不建议使用,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function straightJoin($table, $on = null)
    {
        return $this->join($table, "STRAIGHT_JOIN", $on);
    }

    /**
     * 以替换形式添加记录，正确时返回自增ID，错误返回false
     * @param array $data 数据
     * @return int 正确时返回自增ID，错误返回false
     */
    public function replace(array $data)
    {
        $this->build("REPLACE", $data);
        $id = $this->query($this->sql, $this->params);
        return $id;
    }

    /**
     * 清空记录
     * @return bool 成功时返回true，失败时返回false
     */
    public function truncate()
    {
        if(!empty($this->_where)){
            return false; //TRUNCATE不允许有条件语句
        }
        $this->build("TRUNCATE");
        return $this->query($this->sql) === false ? false : true;
    }

    /**
     * 解析插入多条数值的SQL部分语句，用于数值原样写入
     * @param array $data_set 数据集
     * @param array $fields 可选参数$fields用于指定要插入的字段名数组，这样参数$data_set的元素数组就可以不需要指定键名，方便输入
     * @param array $params 可能要操作的参数数组
     * @return string
     */
    private function parseInsertAllDatas(array $data_set, array $fields = [], array &$params = [])
    {
        if(empty($fields)){  //$fields为空时，$data_set各元素必须带键名，且键名顺序、名称都需要一致
            foreach(array_keys($data_set[0]) as $key){
                $fields[] = $key;
            }
        }
        $values = []; //SQL各单位值填充
        foreach ($data_set as $data){
            $holdes = []; //占位符
            foreach($data as $value){
                $holdes[] = "?";
                $params[] = $value;
            }
            $values[] = '(' . implode(',', $holdes) . ')';
        }
        return '(`' . implode('`,`', $fields) . '`) VALUES ' . implode(',', $values);
    }

    /**
     * 批量插入记录
     * @param array $data_set 数据集
     * @param array $fields 可选参数$fields用于指定要插入的字段名数组，这样参数$data_set的元素数组就可以不需要指定键名，方便输入
     * @return int 返回插入的记录数，错误返回false
     */
    public function insertAll(array $data_set, array $fields = null)
    {
        $params = [];
        $sql = "INSERT INTO `{$this->tablePrefix}{$this->tableName}`{$this->parseInsertAllDatas($data_set, $fields, $params)}";
        $this->sql = $sql;
        $this->params = $params;
        return $this->query($sql, $params);
    }
}
