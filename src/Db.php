<?php


namespace fize\db;

use fize\db\definition\Db as Driver;
use fize\db\definition\Mode;

/**
 * 数据库模型统一调用类
 */
class Db
{

    /**
     * @var Driver
     */
    protected static $db;

    /**
     * @var Mode
     */
    private static $mode;

    /**
     * 初始化
     * @param array $options 配置项
     */
    public static function init(array $options)
    {
        self::$mode = '\\' . __NAMESPACE__ . '\\realization\\' . $options['type'] . '\\Mode';
        $class = self::$mode;
        self::$db = $class::getInstance($options);
    }

    /**
     * 取得一个新的连接
     * @param array $options 配置项
     * @return Driver
     */
    public static function connect(array $options)
    {
        /**
         * @var $class Mode
         */
        $class = '\\fize\\db\\realization\\' . $options['type'] . '\\Mode';
        return $class::getInstance($options);
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组，INSERT/REPLACE返回自增ID，其余返回受影响行数。
     */
    public static function query($sql, array $params = [], callable $callback = null)
    {
        return self::$db->query($sql, $params, $callback);
    }

    /**
     * 开始事务
     */
    public static function startTrans()
    {
        self::$db->startTrans();
    }

    /**
     * 执行事务
     */
    public static function commit()
    {
        self::$db->commit();
    }

    /**
     * 回滚事务
     */
    public static function rollback()
    {
        self::$db->rollback();
    }

    /**
     * 指定当前要操作的表,支持链式调用
     * @param string $name 表名
     * @param string $prefix 表前缀，默认为null表示使用当前前缀
     * @return Driver
     */
    public static function table($name, $prefix = null)
    {
        return self::$db->table($name, $prefix);
    }

    /**
     * 获取最后运行的SQL
     * 仅供日志使用的SQL语句，由于本身存在SQL危险请不要真正用于执行
     * @param bool $real 是否返回最终SQL语句而非预处理语句
     * @return string
     */
    public static function getLastSql($real = false)
    {
        return self::$db->getLastSql($real);
    }
}