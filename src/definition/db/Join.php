<?php

namespace fize\db\definition\db;

/**
 * Builder的join功能模块
 */
trait Join
{
    /**
     * JOIN语句
     * @var string
     */
    protected $_join = "";

    /**
     * JOIN条件,可以使用所有JOIN变种,支持链式调用
     * @param mixed $table 表名，是数组时是形如别名=>表名，且只能有一个元素，否则无效
     * @param string $type JOIN形式,默认为JOIN
     * @param string $on ON条件，建议ON条件单独开来
     * @param string $using USING字段
     * @return $this
     */
    public function join($table, $type = "JOIN", $on = null, $using = null)
    {
        $ttable = '';
        if (is_array($table)) {
            if (count($table) != 1) {
                return $this;
            }
            foreach ($table as $alias => $tname) {
                $ttable = "{$this->_table_($tname)} AS {$this->_table_($alias)}";
            }
        } else {
            $ttable = $this->_table_($table);
        }
        $this->_join .= " {$type} {$ttable}";
        if (!is_null($on)) {
            $this->_join .= " ON {$on}";
        }
        if (!is_null($using)) {
            $this->_join .= " USING({$this->_field_($using)})";
        }
        return $this;
    }

    /**
     * INNER JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function innerJoin($table, $on = null)
    {
        return $this->join($table, "INNER JOIN", $on);
    }

    /**
     * LEFT JOIN条件,支持链式调用
     * @param mixed $table 表名，是数组时是形如别名=>表名，且只能有一个元素，否则无效
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function leftJoin($table, $on = null)
    {
        return $this->join($table, "LEFT JOIN", $on);
    }

    /**
     * RIGHT JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function rightJoin($table, $on = null)
    {
        return $this->join($table, "RIGHT JOIN", $on);
    }
}