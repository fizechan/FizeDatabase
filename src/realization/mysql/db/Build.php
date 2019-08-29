<?php


namespace fize\db\realization\mysql\db;


/**
 * Mysql数据库语句构造trait
 */
trait Build
{

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
    public function buildSQL($action, array $data = [], $clear = true)
    {
        if ($action == 'REPLACE') {
            $params = [];
            $sql = "REPLACE INTO {$this->_table_($this->tablePrefix. $this->tableName)}{$this->parseInsertDatas($data, $params)}";
            $this->sql = $sql;
            $this->params = $params;
            return $sql; //REPLACE语句已完整
        } elseif ($action == 'TRUNCATE') {
            $sql = "TRUNCATE TABLE {$this->_table_($this->tablePrefix . $this->tableName)}";
            $this->sql = $sql;
            return $sql; //TRUNCATE语句已完整
        } else {
            $sql = parent::buildSQL($action, $data, false);
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
}