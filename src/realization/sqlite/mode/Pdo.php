<?php

namespace fize\db\realization\sqlite\mode;


use fize\db\realization\sqlite\Db;
use fize\db\middleware\Pdo as Middleware;

/**
 * PDO方式Sqlite3数据库模型类
 */
class Pdo extends Db
{
    use Middleware;

    /**
     * Pdo constructor.
     * @param string $filename
     * @param string $prefix
     */
    public function __construct($filename, $prefix = "")
    {
        $this->tablePrefix = $prefix;
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