<?php

namespace fize\db\core;

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
    public static function create($mode, array $config);
}
