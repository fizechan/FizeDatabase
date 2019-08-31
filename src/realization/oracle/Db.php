<?php


namespace fize\db\realization\oracle;


use fize\db\definition\Db as Base;


/**
 * Oracle的ORM模型
 */
abstract class Db extends Base
{
    use Feature;

    /**
     * NATURAL JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalJoin($table)
    {
        return $this->join($table, "NATURAL JOIN");
    }

    /**
     * NATURAL LEFT JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalLeftJoin($table)
    {
        return $this->join($table, "NATURAL LEFT JOIN");
    }

    /**
     * NATURAL LEFT OUTER JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalLeftOuterJoin($table)
    {
        return $this->join($table, "NATURAL LEFT OUTER JOIN");
    }

    /**
     * NATURAL RIGHT JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalRightJoin($table)
    {
        return $this->join($table, "NATURAL RIGHT JOIN");
    }

    /**
     * NATURAL RIGHT OUTER JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalRightOuterJoin($table)
    {
        return $this->join($table, "NATURAL RIGHT OUTER JOIN");
    }
}