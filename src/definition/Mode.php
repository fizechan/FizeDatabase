<?php


namespace fize\db\definition;

/**
 * 模式
 *
 * 模式需要实现getInstance来进行模式的统一调用
 */
interface Mode
{
    /**
     * 数据库实例
     * @param array $config 数据库参数选项
     * @return Db
     */
    public static function getInstance(array $config);
}