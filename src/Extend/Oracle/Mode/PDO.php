<?php

namespace Fize\Database\Extend\Oracle\Mode;

use Fize\Database\Extend\Oracle\Db;
use Fize\Database\Middleware\PDO as Middleware;

/**
 * PDO
 *
 * PDO方式(推荐使用)Oracle数据库模型类
 */
class PDO extends Db
{
    use Middleware;

    /**
     * Pdo方式构造
     * @param string   $host    服务器地址，必填
     * @param string   $user    用户名，必填
     * @param string   $pwd     用户密码，必填
     * @param string   $dbname  数据库名，必填
     * @param int|null $port    端口号，选填
     * @param string   $charset 指定编码，选填，默认utf8
     * @param array    $opts    PDO连接的其他选项，选填
     */
    public function __construct(string $host, string $user, string $pwd, string $dbname, int $port = null, string $charset = "utf8", array $opts = [])
    {
        $dsn = "oci:dbname=$host";
        if (!empty($port)) {
            $dsn .= ":$port";
        }
        $dsn .= "/$dbname";
        if (!empty($charset)) {
            $dsn .= ";charset=$charset";
        }
//        var_dump($dsn);
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
