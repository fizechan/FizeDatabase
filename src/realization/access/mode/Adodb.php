<?php

namespace fize\db\realization\access\mode;

use fize\db\realization\access\Db;
use fize\db\middleware\Adodb as Middleware;

/**
 * ADODB方式(推荐使用)连接access数据库
 */
class Adodb extends Db
{

    use Middleware {
        Middleware::query as protected queryAdodb;
    }

    /**
     * Adodb constructor.
     * @see https://www.connectionstrings.com/ace-oledb-12-0/
     * @param string $file 数据库文件路径
     * @param string $pwd 密码
     * @param string $prefix 表前缀
     * @param string $driver 驱动名
     */
    public function __construct($file, $pwd = null, $prefix = "", $driver = null)
    {
        $this->tablePrefix = $prefix;
        if (is_null($driver)) {
            $driver = "Microsoft.ACE.OLEDB.12.0";
        }
        $dsn = "Provider={$driver};Data Source=" . realpath($file) . ";";
        if( $pwd ) {
            $dsn .= "Jet OLEDB:Database Password=" . $pwd . ";";
        }
        $this->adodbConstruct($dsn, 65001);
    }

    /**
     * 析构时释放ADODB资源
     */
    public function __destruct()
    {
        $this->adodbDestruct();
        parent::__destruct();
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持模拟问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组(错误返回false)，INSERT/REPLACE返回自增ID，其余返回受影响行数
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        if (stripos($sql, "INSERT") === 0) {
            $rst = $this->queryAdodb($sql, $params, $callback);
            if ($rst === false) {
                return false;
            }
            //获取最后的自增ID
            $id_sql = "SELECT @@IDENTITY AS id";
            $id_rst = $this->queryAdodb($id_sql);
            return $id_rst[0]['id'];
        } else {
            return $this->queryAdodb($sql, $params, $callback);
        }
    }
}