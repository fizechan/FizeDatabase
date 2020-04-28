<?php

namespace fize\db\extend\access;

use fize\db\core\Db as Base;

/**
 * 数据库
 *
 * 使用该类库需要安装access数据库引擎(AccessDatabaseEngine)。
 * 如果使用32位驱动，则IIS程序池还得开启32位支持。
 */
abstract class Db extends Base
{
    use Feature;

    /**
     * 自己实现的安全化值
     * @param mixed $value 要安全化的值
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . str_replace("'", "''", $value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'NULL';
        }
        return $value;
    }

    /**
     * @var int 指定每页记录集数量，为0时表示不指定，全部返回。
     */
    protected $size = 0;

    /**
     * @var int 指定游标指针位移，为null时不指定，不移动。
     */
    protected $offset = null;

    /**
     * @var string TOP语句
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
     * @param int $rows   要返回的记录数
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
