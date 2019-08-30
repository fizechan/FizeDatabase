<?php


namespace fize\db;

use fize\db\definition\Query as Driver;

/**
 * 查询器模型统一调用类
 * @package fize\db
 */
class Query
{

    /**
     * @var array 配置参数
     */
    protected static $options;

    /**
     * @var string 实际使用Query类名
     */
    protected static $class;

    /**
     * 初始化
     * @param array $options 配置项
     */
    public static function init(array $options)
    {
        self::$options = $options;
        self::$class = '\\fize\\db\\realization\\' . $options['type'] . '\\Query';
    }

    /**
     * 设定当前操作对象
     * @param string $object 操作对象，通常为字段名
     * @return Driver
     */
    public static function object($object = null)
    {
        $query = new self::$class($object);
        return $query;
    }

    /**
     * 设定当前操作字段
     * 实际上是object方法的别名
     * @param string $field_name 字段名
     * @return Driver
     */
    public static function field($field_name)
    {
        $query = new self::$class($field_name);
        return $query;
    }

    /**
     * 解析一个条件数组，返回Query
     * @param array $maps 一定格式的条件数组
     * @return Driver
     */
    public static function analyze(array $maps)
    {
        /**
         * @var $query Driver
         */
        $query = new self::$class();
        return $query->analyze($maps);
    }

    /**
     * 以AND形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param string $logic 组合逻辑
     * @param array $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return Driver
     */
    public static function qMerge($logic, ...$querys)
    {
        /**
         * @var $query Driver
         */
        $query = $querys[0];
        if (is_array($querys[0])) {
            $query = new self::$class();
            $query->analyze($querys[0]);
        }

        for ($i = 1; $i < count($querys); $i++) {
            /**
             * @var $query2 Driver
             */
            $query2 = $querys[$i];
            if (is_array($querys[$i])) {
                $query2 = new self::$class();
                $query2->analyze($querys[$i]);
            }
            $query->qMerge($logic, $query2);
        }
        return $query;
    }

    /**
     * 以AND形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param array $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return Driver
     */
    public static function qAnd(...$querys)
    {
        return self::qMerge('AND', ...$querys);
    }

    /**
     * 以OR形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param array $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return Driver
     */
    public static function qOr(...$querys)
    {
        return self::qMerge('OR', ...$querys);
    }
}