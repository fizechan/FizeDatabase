<?php

namespace fize\db\realization\access;

use fize\db\definition\Db as Base;


/**
 * Access数据库类
 * @notice 使用该类库需要安装access数据库引擎(AccessDatabaseEngine)，如果使用32位驱动，则IIS程序池还得开启32位支持。
 */
abstract class Db extends Base
{
    use Feature;

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
     * TOP语句
     * @var string
     */
    protected $top = "";

    /**
     * 设置TOP,支持链式调用
     * @param int $rows 要返回的记录数
     * @return $this
     */
    public function top($rows)
    {
        $this->top = $rows;
        return $this;
    }

    /**
     * 模拟LIMIT语句,支持链式调用
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