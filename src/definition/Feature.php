<?php


namespace fize\db\definition;

/**
 * 数据库特征项trait
 */
trait Feature
{

    /**
     * 格式化数据表名称，继承类根据实际使用数据库来进行重写
     * @param string $str 待格式化字符串
     * @return string
     */
    protected function _table_($str)
    {
        return $str;
    }

    /**
     * 格式化字段名称，继承类根据实际使用数据库来进行重写
     * @param string $str 待格式化字符串
     * @return string
     */
    protected function _field_($str)
    {
        return $str;
    }
}