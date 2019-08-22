<?php


namespace fize\db\realization\pgsql\db;


trait Join
{
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
}
