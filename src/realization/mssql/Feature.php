<?php

namespace fize\db\realization\mssql;


/**
 * Mssql数据库特征项trait
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
        if(strpos($str, '[') === 0)
        {
            return $str;
        }
        elseif (stripos($str, ' SELECT ') !== false)
        {
            return $str;
        }
        elseif (stripos($str, ' AS ') !== false)
        {
            return $str;
        }
        else
        {
            return "[{$str}]";
        }
    }

    /**
     * 格式化字段名称
     * @param string $str 待格式化字符串，原则上$str是否已格式化应是黑盒未知的。
     * @return string
     */
    protected function formatField($str)
    {
        if($str === '*')
        {
            return '*';
        }
        elseif(strpos($str, '[') === 0)
        {
            return $str;
        }
        elseif (stripos($str, ' SELECT ') !== false)
        {
            return $str;
        }
        elseif (stripos($str, ' AS ') !== false)
        {
            return $str;
        }
        elseif (substr($str, -1) === ')')
        {
            return $str;
        }
        else
        {
            return "[{$str}]";
        }
    }
}