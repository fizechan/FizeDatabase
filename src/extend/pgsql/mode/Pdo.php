<?php

namespace fize\database\extend\pgsql\mode;

use fize\database\extend\pgsql\Db;
use fize\database\middleware\Pdo as Middleware;

/**
 * PDO
 *
 * PDO方式(推荐使用)PostgreSQL数据库模型类
 */
class Pdo extends Db
{
    use Middleware;

    /**
     * PDO方式PostgreSQL数据库模型类
     * @param string   $host   服务器地址
     * @param string   $user   用户名
     * @param string   $pwd    用户密码
     * @param string   $dbname 数据库名
     * @param int|null $port   端口号，选填，PostgreSQL默认是5432
     * @param array    $opts   PDO连接的其他选项，选填
     */
    public function __construct(string $host, string $user, string $pwd, string $dbname, int $port = null, array $opts = [])
    {
        $dsn = "pgsql:host=$host;dbname=$dbname";
        if (!empty($port)) {
            $dsn .= ";port=$port";
        }
        $this->pdoConstruct($dsn, $user, $pwd, $opts);
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
