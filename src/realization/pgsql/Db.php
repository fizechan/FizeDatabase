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
}
