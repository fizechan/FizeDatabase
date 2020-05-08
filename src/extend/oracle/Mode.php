<?php

namespace fize\db\extend\oracle;

use fize\db\extend\oracle\mode\Oci;
use fize\db\extend\oracle\mode\Odbc;
use fize\db\extend\oracle\mode\Pdo;

/**
 * 模式
 *
 * Oracle数据库模型类
 */
class Mode
{

    /**
     * oci方式构造
     * @param string $username          用户名
     * @param string $password          密码
     * @param string $connection_string 连接串
     * @param string $character_set     编码
     * @param int    $session_mode      会话模式
     * @param int    $connect_type      连接模式
     * @return Oci
     */
    public static function oci($username, $password, $connection_string = null, $character_set = null, $session_mode = null, $connect_type = 1)
    {
        return new Oci($username, $password, $connection_string, $character_set, $session_mode, $connect_type);
    }

    /**
     * ODBC方式构造
     * @param string $user    用户名
     * @param string $pwd     用户密码
     * @param string $sid     连接串
     * @param mixed  $port    端口号，选填，Oracle默认是1521
     * @param string $charset 指定编码，选填，默认utf8
     * @param string $driver  指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($user, $pwd, $sid, $port = "", $charset = "utf8", $driver = null)
    {
        return new Odbc($user, $pwd, $sid, $port, $charset, $driver);
    }

    /**
     * pdo方式构造
     * @param string $host    主机
     * @param string $user    用户名
     * @param string $pwd     密码
     * @param string $dbname  数据库名
     * @param int    $port    端口
     * @param string $charset 编码
     * @param array  $opts    其他选项
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $port = null, $charset = "utf8", array $opts = [])
    {
        return new Pdo($host, $user, $pwd, $dbname, $port, $charset, $opts);
    }
}
