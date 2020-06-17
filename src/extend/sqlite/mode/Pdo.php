<?php

namespace fize\database\extend\sqlite\mode;

use fize\database\extend\sqlite\Db;
use fize\database\middleware\Pdo as Middleware;

/**
 * PDO
 *
 * PDO方式Sqlite3数据库模型类
 */
class Pdo extends Db
{
    use Middleware;

    /**
     * Pdo constructor.
     * @param string $filename 数据库文件路径
     */
    public function __construct($filename)
    {
        $dsn = "sqlite:{$filename}";
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
