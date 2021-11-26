<?php

namespace Fize\Database\Extend\SQLite\Mode;

use Fize\Database\Extend\SQLite\Db;
use Fize\Database\Middleware\PDO as Middleware;

/**
 * PDO
 *
 * PDO方式Sqlite3数据库模型类
 */
class PDO extends Db
{
    use Middleware;

    /**
     * 初始化
     * @param string $filename 数据库文件路径
     */
    public function __construct(string $filename)
    {
        $dsn = "sqlite:$filename";
        $this->pdoConstruct($dsn, '', '');
    }

    /**
     * 析构时关闭PDO
     */
    public function __destruct()
    {
        $this->pdoDestruct();
        parent::__destruct();
    }
}
