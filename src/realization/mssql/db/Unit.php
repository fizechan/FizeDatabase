<?php


namespace fize\db\realization\mssql\db;


trait Unit
{
    /**
     * 指定每页记录集数量，为0时表示不指定，全部返回。
     * @var int
     */
    protected $size = 0;

    /**
     * 指定游标指针位移，为null时不指定，不移动。
     * @var int
     */
    protected $offset = null;

    /**
     * 设置TOP,支持链式调用
     * @param int $rows 要返回的记录数
     * @return $this
     */
    public function top($rows)
    {
        $this->size = $rows;
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
        $this->size = $rows;
        $this->offset = $offset;
        return $this;
    }
}