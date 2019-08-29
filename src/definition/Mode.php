<?php


namespace fize\db\definition;

/**
 * 数据库使用模式
 * @package fize\db\definition
 */
interface Mode
{
    /**
     * 数据库实例
     * @param array $options 数据库参数选项
     * @return Db
     */
    public static function getInstance(array $options);
}