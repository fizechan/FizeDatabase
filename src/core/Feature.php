<?php

namespace fize\database\core;

/**
 * 特征项
 *
 * 特征项定义
 */
trait Feature
{

    /**
     * 格式化数据表名称
     * @param string $str 待格式化字符串，原则上$str是否已格式化应是黑盒未知的。
     * @return string
     */
    protected function formatTable(string $str): string
    {
        return $str;
    }

    /**
     * 格式化字段名称
     * @param string $str 待格式化字符串，原则上$str是否已格式化应是黑盒未知的。
     * @return string
     */
    protected function formatField(string $str): string
    {
        return $str;
    }
}
