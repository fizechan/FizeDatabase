<?php

namespace Fize\Database\Core;

/**
 * 模式工厂
 */
interface ModeFactoryInterface
{
    /**
     * 创建实例
     * @param string $mode   连接模式
     * @param array  $config 参数选项
     * @return Db
     */
    public static function create(string $mode, array $config);
}
