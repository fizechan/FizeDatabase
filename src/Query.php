<?php

namespace fize\database;

use fize\database\core\Query as CoreQuery;

/**
 * 查询器
 *
 * 通常我们直接调用查询器的静态方法来构建SQL查询条件语句
 */
class Query
{

    /**
     * @var string 实际使用Query类名
     */
    protected static $class;

    /**
     * 初始化
     * @param string $db_type 数据库类型
     */
    public function __construct(string $db_type)
    {
        self::$class = '\\' . __NAMESPACE__ . '\\extend\\' . $db_type . '\\Query';
    }

    /**
     * 获取指定数据库类型的查询器对象
     * @param string      $db_type 数据库类型
     * @param string|null $object  要进行判断的对象，一般为字段名
     * @return CoreQuery
     */
    public static function construct(string $db_type, string $object = null): CoreQuery
    {
        $class = '\\' . __NAMESPACE__ . '\\extend\\' . $db_type . '\\Query';
        return new $class($object);
    }

    /**
     * 设定当前操作对象
     *
     * @param string|null $object 操作对象，通常为字段名
     * @return CoreQuery
     * @deprecated 待移除
     */
    public static function object(string $object = null): CoreQuery
    {
        return new self::$class($object);
    }

    /**
     * 设定当前操作字段
     *
     * 实际上是object方法的别名
     * @param string $field_name 字段名
     * @return CoreQuery
     */
    public static function field(string $field_name): CoreQuery
    {
        return new self::$class($field_name);
    }

    /**
     * 解析一个条件数组，返回Query
     * @param array $maps 一定格式的条件数组
     * @return CoreQuery
     */
    public static function analyze(array $maps): CoreQuery
    {
        /**
         * @var CoreQuery $query
         */
        $query = new self::$class();
        return $query->analyze($maps);
    }

    /**
     * 以AND形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param string $logic  组合逻辑
     * @param array  $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return CoreQuery
     */
    public static function qMerge(string $logic, ...$querys): CoreQuery
    {
        /**
         * @var CoreQuery $query
         */
        $query = $querys[0];
        if (is_array($querys[0])) {
            $query = new self::$class();
            $query->analyze($querys[0]);
        }

        for ($i = 1; $i < count($querys); $i++) {
            /**
             * @var CoreQuery $query2
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
     * @return CoreQuery
     */
    public static function and(...$querys): CoreQuery
    {
        return self::qMerge('AND', ...$querys);
    }

    /**
     * 以OR形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param array $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return CoreQuery
     */
    public static function or(...$querys): CoreQuery
    {
        return self::qMerge('OR', ...$querys);
    }
}
