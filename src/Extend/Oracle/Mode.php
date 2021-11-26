<?php

namespace Fize\Database\Extend\Oracle;

use Fize\Database\Extend\Oracle\mode\OCI;
use Fize\Database\Extend\Oracle\mode\ODBC;
use Fize\Database\Extend\Oracle\mode\PDO;

/**
 * 模式
 *
 * Oracle数据库模型类
 */
class Mode
{

    /**
     * oci方式构造
     * @param string      $username          用户名
     * @param string      $password          密码
     * @param string|null $connection_string 连接串
     * @param string|null $character_set     编码
     * @param int|null    $session_mode      会话模式
     * @param int         $connect_type      连接模式
     * @return OCI
     */
    public static function oci(string $username, string $password, string $connection_string = null, string $character_set = null, int $session_mode = null, int $connect_type = 1): OCI
    {
        return new OCI($username, $password, $connection_string, $character_set, $session_mode, $connect_type);
    }

    /**
     * ODBC方式构造
     * @param string      $user    用户名
     * @param string      $pwd     用户密码
     * @param string      $sid     连接串
     * @param mixed       $port    端口号，选填，Oracle默认是1521
     * @param string      $charset 指定编码，选填，默认utf8
     * @param string|null $driver  指定ODBC驱动名称。
     * @return ODBC
     */
    public static function odbc(string $user, string $pwd, string $sid, $port = "", string $charset = "utf8", string $driver = null): ODBC
    {
        return new ODBC($user, $pwd, $sid, $port, $charset, $driver);
    }

    /**
     * pdo方式构造
     * @param string   $host    主机
     * @param string   $user    用户名
     * @param string   $pwd     密码
     * @param string   $dbname  数据库名
     * @param int|null $port    端口
     * @param string   $charset 编码
     * @param array    $opts    其他选项
     * @return PDO
     */
    public static function pdo(string $host, string $user, string $pwd, string $dbname, int $port = null, string $charset = "utf8", array $opts = []): PDO
    {
        return new PDO($host, $user, $pwd, $dbname, $port, $charset, $opts);
    }
}
