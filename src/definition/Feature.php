<?php


namespace fize\db\definition;

/**
 * 数据库特征项
 * @package fize\db\definition
 */
trait Feature
{

    /**
     * 格式化数据表名称
     * @param string $str 待格式化字符串，原则上$str是否已格式化应是黑盒未知的。
     * @return string
     */
    protected function formatTable($str)
    {
        return $str;
    }

    /**
     * 格式化字段名称
     * @param string $str 待格式化字符串，原则上$str是否已格式化应是黑盒未知的。
     * @return string
     */
    protected function formatField($str)
    {
        return $str;
    }
}