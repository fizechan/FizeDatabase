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
     * @var Driver Query对象
     */
    protected static $query;

    /**
     * @var array 配置参数
     */
    protected static $options;

    /**
     * 初始化
     * @param array $options 配置项
     */
    public static function init(array $options)
    {
        self::$options = $options;
        $class = '\\fize\\db\\realization\\' . $options['type'] . '\\Query';
        self::$query = new $class();
    }

    /**
     * 查询器单例
     * @return Driver
     */
    public static function getInstance()
    {
        return self::$query;
    }

    /**
     * 获取一个新的Query对象
     * @param string $object 要进行判断的对象，一般为字段名
     * @return Driver
     */
    public static function construct($object = null)
    {
        $class = '\\fize\\db\\realization\\' . self::$options['type'] . '\\Query';
        return new $class($object);
    }

    /**
     * 设定当前操作对象
     * @param string $object 操作对象，通常为字段名
     * @return Driver
     */
    public static function object($object)
    {
        return self::$query->object($object);
    }

    /**
     * 设定当前操作字段
     * 实际上是object方法的别名
     * @param string $field_name 字段名
     * @return Driver
     */
    public static function field($field_name)
    {
        return self::$query->field($field_name);
    }

    /**
     * 解析一个条件数组，返回Query
     * @param array $maps 一定格式的条件数组
     * @return Driver
     */
    public static function analyze(array $maps)
    {
        return self::$query->analyze($maps);
    }

    /**
     * 以AND形式组合Query对象,或者指可以使用analyze()的数组
     * @param mixed $query 可以是Query对象或者指可以使用analyze()的数组
     * @return Driver
     */
    public function qAnd($query)
    {
        return self::$query->qAnd($query);
    }

    /**
     * 以OR形式组合Query对象,或者指可以使用analyze()的数组
     * @param mixed $query 可以是Query对象或者指可以使用analyze()的数组
     * @return Driver
     */
    public function qOr($query)
    {
        return self::$query->qOr($query);
    }
}