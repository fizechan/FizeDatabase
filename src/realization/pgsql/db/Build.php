<?php


namespace fize\db\realization\pgsql\db;


/**
 * PostgreSQL数据库语句构造trait
 */
trait Build
{

    /**
     * 清空当前条件，以便于下次查询
     */
    protected function clear()
    {
        parent::clear();
        $this->_limit = "";
        $this->_lock = false;
        $this->_lock_sql = "";
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
            $sql = "REPLACE INTO {$this->_table_($this->_tablePrefix. $this->_tableName)}{$this->parseInsertDatas($data, $params)}";
            $this->_sql = $sql;
            $this->_params = $params;
            return $sql; //REPLACE语句已完整
        } elseif ($action == 'TRUNCATE') {
            $sql = "TRUNCATE TABLE {$this->_table_($this->_tablePrefix . $this->_tableName)}";
            $this->_sql = $sql;
            return $sql; //TRUNCATE语句已完整
        } else {
            $sql = parent::buildSQL($action, $data, false);
        }
        if (!empty($this->_limit)) {
            $sql .= " LIMIT {$this->_limit}";
        }
        $this->_sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }
}
