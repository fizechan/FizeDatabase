<?php

namespace fize\db\extend\oracle\mode;

use fize\db\extend\oracle\Db;
use fize\db\middleware\Odbc as Middleware;

/**
 * ODBC
 *
 * ODBC方式Oracle数据库模型类
 * 注意ODBC返回的类型都为字符串格式(null除外)
 * @todo 未测试通过，暂无法使用
 */
class Odbc extends Db
{
    use Middleware;

    /**
     * 构造
     * @param string $user    用户名
     * @param string $pwd     用户密码
     * @param string $sid     连接串
     * @param mixed  $port    端口号，选填，Oracle默认是1521
     * @param string $charset 指定编码，选填，默认utf8
     * @param string $driver  指定ODBC驱动名称。
     */
    public function __construct($user, $pwd, $sid, $port = "", $charset = "utf8", $driver = null)
    {
        if (is_null($driver)) {  //默认驱动名
            //$driver = "{Oracle in OraClient11g_home1}";
            $driver = "{Oracle in OraDB12Home1}";
        }
        $dsn = "DRIVER={$driver};SERVER='{$sid}';UID='{$user}';PWD='{$pwd}';CHARSET={$charset}";
        //$dsn = "DRIVER={$driver};SERVER={$sid};UID={$user};PWD={$pwd};";
        //$dsn = "DRIVER={$driver};SERVER={$sid};";
        if (!empty($port)) {
            $dsn .= ";PORT={$port}";
        }
        $this->odbcConstruct($dsn, $user, $pwd);
    }

    /**
     * 析构时关闭ODBC
     */
    public function __destruct()
    {
        $this->odbcDestruct();
        parent::__destruct();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在oracle中必须指定
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $sql = "SELECT {$name}.currval FROM dual";
        $stmt = $this->driver->exec($sql);
        return $stmt->result(1);
    }
}
