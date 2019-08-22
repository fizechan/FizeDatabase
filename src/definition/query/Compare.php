<?php

namespace fize\db\definition\query;

/**
 * DB比较条件查询
 */
trait Compare
{
    /**
     * 使用条件“大于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function gt($value)
    {
        return $this->condition(">", $value);
    }

    /**
     * 使用条件“大于等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function egt($value)
    {
        return $this->condition(">=", $value);
    }

    /**
     * 使用条件“小于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function lt($value)
    {
        return $this->condition("<", $value);
    }

    /**
     * 使用条件“小于等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function elt($value)
    {
        return $this->condition("<=", $value);
    }

    /**
     * 使用条件“等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function eq($value)
    {
        return $this->condition("=", $value);
    }

    /**
     * 使用条件“不等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function neq($value)
    {
        return $this->condition("<>", $value);
    }
}