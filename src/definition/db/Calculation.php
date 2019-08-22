<?php

namespace fize\db\definition\db;

/**
 * 数据库模型抽象类计算功能
 */
trait Calculation
{
    /**
     * COUNT查询
     * @param string $field 字段名
     * @return int
     */
    public function count($field = "*")
    {
        return $this->value("COUNT({$this->_field_($field)})", 0, true);
    }

    /**
     * SUM查询
     * @param string $field 字段名
     * @return int
     */
    public function sum($field)
    {
        $sum = $this->value("SUM({$this->_field_($field)})", 0, true);
        if(is_null($sum)){ //求SUM时，并不希望得到NULL值
            $sum = 0;
        }
        return $sum;
    }

    /**
     * MIN查询
     * @param string $field 字段名
     * @param bool $force 强制转为数字类型
     * @return mixed 如果$force为true时真返回数字类型
     */
    public function min($field, $force = true)
    {
        return $this->value("MIN({$this->_field_($field)})", null, $force);
    }

    /**
     * MAX查询
     * @param string $field 字段名
     * @param bool $force 强制转为数字类型
     * @return mixed 如果$force为true时真返回数字类型
     */
    public function max($field, $force = true)
    {
        return $this->value("MAX({$this->_field_($field)})", null, $force);
    }

    /**
     * AVG查询
     * @param string $field 字段名
     * @return mixed
     */
    public function avg($field)
    {
        return $this->value("AVG({$this->_field_($field)})", 0, true);
    }
}