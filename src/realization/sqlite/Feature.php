<?php


namespace fize\db\realization\sqlite;


/**
 * sqlite3数据库特征项trait
 */
trait Feature
{

    /**
     * 格式化数据表名称
     * @param string $str 待格式化字符串，原则上$str是否已格式化应是黑盒未知的。
     * @return string
     */
    protected function _table_($str)
    {
        return "{$str}";
    }

    /**
     * 格式化字段名称
     * @param string $str 待格式化字符串，原则上$str是否已格式化应是黑盒未知的。
     * @return string
     */
    protected function _field_($str)
    {
        return "{$str}";
    }
}