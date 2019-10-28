<?php


namespace fize\db\realization\oracle;

use fize\db\definition\Mode as ModeInterface;
use fize\db\exception\Exception;
use fize\db\realization\oracle\mode\Oci;
use fize\db\realization\oracle\mode\Pdo;

/**
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
     * @param array $config 数据库参数选项
     * @return Db
     * @throws Exception
     */
    public static function getInstance(array $config)
    {
        $mode = isset($config['mode']) ? $config['mode'] : 'oci';
        $db_cfg = $config['config'];
        switch ($mode) {
            case 'oci':
                $connection_string = isset($db_cfg['connection']) ? $db_cfg['connection'] : null;
                $charset = isset($db_cfg['charset']) ? $db_cfg['charset'] : null;
                $session_mode = isset($db_cfg['session_mode']) ? $db_cfg['session_mode'] : null;
                $connect_type = isset($db_cfg['connect_type']) ? $db_cfg['connect_type'] : 1;
                return self::oci($db_cfg['username'], $db_cfg['password'], $connection_string, $charset, $session_mode, $connect_type);
            case 'pdo':
                $port = isset($db_cfg['port']) ? $db_cfg['port'] : '';
                $charset = isset($db_cfg['charset']) ? $db_cfg['charset'] : 'utf8';
                $opts = isset($db_cfg['opts']) ? $db_cfg['opts'] : [];
                return self::pdo($db_cfg['host'], $db_cfg['user'], $db_cfg['password'], $db_cfg['dbname'], $port, $charset, $opts);
            default:
                throw new Exception("error db mode: {$mode}");
        }
    }
}