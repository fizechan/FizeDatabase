<?php

namespace fize\db\definition\db;

/**
 * Builder的union功能模块
 */
trait Union
{
    /**
     * UNION语句
     * @var string
     */
    protected $_union = "";

    /**
     * UNION语句,支持链式调用
     * @param string $sql 要UNION的SQL语句
     * @param string $union_type 类型，可选值UNION、UNION ALL、UNION DISTINCT，默认UNION
     * @return $this
     */
    public function union($sql, $union_type = "UNION")
    {
        $this->_union .= " {$union_type} {$sql}";
        return $this;
    }

    /**
     * UNION ALL语句,支持链式调用
     * @param string $sql 要UNION ALL的SQL语句
     * @return $this
     */
    public function unionAll($sql)
    {
        return $this->union($sql, 'UNION ALL');
    }

    /**
     * UNION DISTINCT语句,支持链式调用
     * @param string $sql 要UNION DISTINCT的SQL语句
     * @return $this
     */
    public function unionDistinct($sql)
    {
        return $this->union($sql, 'UNION DISTINCT');
    }

}