<?php

namespace fize\db\realization\pgsql\mode;


use fize\db\realization\pgsql\Db;
use fize\db\middleware\pdo\Middleware;

/**
 * PDO方式(推荐使用)MySQL数据库模型类
 */
class Pdo extends Db
{
    use Middleware;

    /**
     * Pdo方式构造必须实例化$this->_pdo
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param int $port 端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array $opts PDO连接的其他选项，选填
     * @param string $socket 指定应使用的套接字或命名管道,windows不可用，选填，默认不指定
     */
    public function __construct($host, $user, $pwd, $dbname, $prefix = "", $port = null, $charset = "utf8", array $opts = [], $socket = null)
    {
        $this->_tablePrefix = $prefix;
        $dsn = "mysql:host={$host};dbname={$dbname}";
        if (!empty($port)) {
            $dsn .= ";port={$port}";
        }
        if (!empty($socket)) {
            $dsn .= ";unix_socket={$socket}";
        }
        if (!empty($charset)) {
            $dsn .= ";charset={$charset}";
        }
        $this->construct($dsn, $user, $pwd, $opts);
    }
}
