<?php


namespace fize\database;

use fize\database\core\Db as CoreDb;
use fize\database\core\ModeFactoryInterface;

/**
 * 数据库
 *
 * 使用该类静态方法来便捷的进行SQL操作
 */
class Db
{

    /**
     * @var CoreDb DB对象
     */
    protected static $db;

    /**
     * 初始化
     * @param string $type   数据库类型
     * @param array  $config 数据库配置项
     * @param string $mode   连接模式
     */
    public function __construct($type, array $config, $mode = null)
    {
        /**
         * @var ModeFactoryInterface $class
         */
        $class = '\\' . __NAMESPACE__ . '\\extend\\' . $type . '\\ModeFactory';
        self::$db = $class::create($mode, $config);
        new Query($type);
    }

    /**
     * 取得一个新的连接
     * @param string $type   数据库类型
     * @param array  $config 数据库配置项
     * @param string $mode   连接模式
     * @return CoreDb
     */
    public static function connect($type, array $config, $mode = null)
    {
        /**
         * @var ModeFactoryInterface $class
         */
        $class = '\\' . __NAMESPACE__ . '\\extend\\' . $type . '\\ModeFactory';
        return $class::create($mode, $config);
    }

    /**
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持问号预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array 返回结果数组
     */
    public static function query($sql, array $params = [], callable $callback = null)
    {
        return self::$db->query($sql, $params, $callback);
    }

    /**
     * 执行一个SQL语句
     * @param string $sql    SQL语句，支持问号预处理语句
     * @param array  $params 可选的绑定参数
     * @return int 返回受影响行数
     */
    public function execute($sql, array $params = [])
    {
        return self::$db->execute($sql, $params);
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
     * @param string $name   表名
     * @param string $prefix 表前缀，默认为null表示使用当前前缀
     * @return CoreDb
     */
    public static function table($name, $prefix = null)
    {
        return self::$db->table($name, $prefix);
    }

    /**
     * 获取最后运行的SQL
     *
     * 仅供日志使用的SQL语句，由于本身存在SQL危险请不要真正用于执行
     * @param bool $real 是否返回最终SQL语句而非预处理语句
     * @return string
     */
    public static function getLastSql($real = false)
    {
        return self::$db->getLastSql($real);
    }
}
