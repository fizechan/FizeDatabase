<?php

namespace fize\db\realization\access\db;


/**
 * Access数据库的查找功能
 */
trait Search
{

    /**
     * 指定每页记录集数量，为0时表示不指定，全部返回。
     * @var int
     */
    protected $_size = 0;

    /**
     * 指定游标指针位移，为null时不指定，不移动。
     * @var int
     */
    protected $_offset = null;

    /**
     * TOP语句
     * @var string
     */
    protected $_top = "";

    /**
     * 设置TOP,支持链式调用
     * @param int $rows 要返回的记录数
     * @return $this
     */
    public function top($rows)
    {
        $this->_top = $rows;
        return $this;
    }

    /**
     * 模拟MySQL的LIMIT语句,支持链式调用
     * @param int $rows 要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    public function limit($rows, $offset = null)
    {
        $this->_size = $rows;
        $this->_offset = $offset;
        return $this;
    }

    /**
     * 执行查询，获取单条记录
     * @return array 如果无记录则返回null
     */
    public function findOrNull()
    {
        $this->top(1);
        $this->buildSQL("SELECT");
        $result = $this->query($this->_sql, $this->_params);
        if (is_array($result) && isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }
}