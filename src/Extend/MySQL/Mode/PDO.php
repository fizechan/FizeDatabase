<?php

namespace Fize\Database\Extend\MySQL\Mode;


use Fize\Database\Extend\MySQL\Db;
use Fize\Database\Middleware\PDO as Middleware;

/**
 * PDO
 *
 * PDO方式(推荐使用)MySQL数据库模型类
 */
class PDO extends Db
{
    use Middleware;

    /**
     * Pdo方式构造
     * @param string      $host    服务器地址
     * @param string      $user    用户名
     * @param string      $pwd     用户密码
     * @param string      $dbname  数据库名
     * @param int|null    $port    端口号，选填，MySQL默认是3306
     * @param string      $charset 指定编码，选填，默认utf8
     * @param array       $opts    PDO连接的其他选项，选填
     * @param string|null $socket  指定应使用的套接字或命名管道,windows不可用，选填，默认不指定
     */
    public function __construct(string $host, string $user, string $pwd, string $dbname, int $port = null, string $charset = "utf8", array $opts = [], string $socket = null)
    {
        $dsn = "mysql:host=$host;dbname=$dbname";
        if (!empty($port)) {
            $dsn .= ";port=$port";
        }
        if (!empty($socket)) {
            $dsn .= ";unix_socket=$socket";
        }
        if (!empty($charset)) {
            $dsn .= ";charset=$charset";
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
