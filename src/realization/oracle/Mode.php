<?php


namespace fize\db\realization\oracle;

use fize\db\definition\Mode as ModeInterface;
use fize\db\exception\Exception;
use fize\db\realization\oracle\mode\Oci;
use fize\db\realization\oracle\mode\Odbc;
use fize\db\realization\oracle\mode\Pdo;

/**
 * 模式
 *
 * Oracle数据库模型类
 */
class Mode implements ModeInterface
{

    /**
     * oci方式构造
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $connection_string 连接串
     * @param string $character_set 编码
     * @param int $session_mode 会话模式
     * @param int $connect_type 连接模式
     * @return Oci
     */
    public static function oci($username, $password, $connection_string = null, $character_set = null, $session_mode = null, $connect_type = 1)
    {
        return new Oci($username, $password, $connection_string, $character_set, $session_mode, $connect_type);
    }

    /**
     * ODBC方式构造
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $sid 连接串
     * @param mixed $port 端口号，选填，Oracle默认是1521
     * @param string $charset 指定编码，选填，默认utf8
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($user, $pwd, $sid, $port = "", $charset = "utf8", $driver = null)
    {
        return new Odbc($user, $pwd, $sid, $port, $charset, $driver);
    }

    /**
     * pdo方式构造
     * @param string $host 主机
     * @param string $user 用户名
     * @param string $pwd 密码
     * @param string $dbname 数据库名
     * @param int $port 端口
     * @param string $charset 编码
     * @param array $opts 其他选项
     * @return Pdo
     */
    public static function pdo($host, $user, $pwd, $dbname, $port = null, $charset = "utf8", array $opts = [])
    {
        return new Pdo($host, $user, $pwd, $dbname, $port, $charset, $opts);
    }

    /**
     * 数据库实例
     * @param string $mode 连接模式
     * @param array $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function getInstance($mode, array $config)
    {
        $mode = $mode ? $mode : 'pdo';
        $default_config = [
            'port'         => '',
            'charset'      => 'UTF8',
            'prefix'       => '',
            'session_mode' => null,
            'connect_type' => 1,
            'opts'         => [],
            'driver'       => null
        ];
        $config = array_merge($default_config, $config);
        switch ($mode) {
            case 'oci':
                $connection_string = $config['host'];
                if ($config['port']) {
                    $connection_string .= ':' . $config['port'];
                }
                $connection_string .= '/' . $config['dbname'];
                $db = self::oci($config['username'], $config['password'], $connection_string, $config['charset'], $config['session_mode'], $config['connect_type']);
                break;
            case 'odbc':
                $sid = $config['host'];
                if ($config['port']) {
                    $sid .= ':' . $config['port'];
                }
                $sid .= '/' . $config['dbname'];
                $db = self::odbc($config['username'], $config['password'], $sid, $config['port'], $config['charset'], $config['driver']);
                break;
            case 'pdo':
                $db = self::pdo($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port'], $config['charset'], $config['opts']);
                break;
            default:
                throw new Exception("error db mode: {$mode}");
        }
        $db->prefix($config['prefix']);
        return $db;
    }
}