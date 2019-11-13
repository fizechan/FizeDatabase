<?php


namespace fize\db\realization\mssql\mode;

use fize\db\realization\mssql\Db;
use fize\db\realization\mssql\mode\driver\Sqlsrv as Driver;


/**
 * MSSQL的ORM模型
 *
 * php_sqlsrv.dll需要本地客户端支持，不同版本使用的客户端不同，可以在错误信息中获取相关资料。
 * php_sqlsrv.dll由微软官方提供技术支持，推荐使用。
 */
class Sqlsrv extends Db
{

    /**
     * @var Driver 使用的MSSQL对象
     */
    protected $driver = null;

    /**
     * 构造
     * @param string $host 数据库服务器
     * @param string $user 数据库登录账户
     * @param string $pwd 数据库登录密码
     * @param string $dbname 数据库名
     * @param mixed $port 数据库服务器端口，选填，默认是1433(默认设置)
     * @param string $charset 指定数据库编码，默认GBK,(不区分大小写)
     */
    public function __construct($host, $user, $pwd, $dbname, $port = "", $charset = "GBK")
    {
        $charset = strtoupper($charset);
        $charset_map = [
            'UTF8' => 'UTF-8',
        ];
        $charset = isset($charset_map[$charset]) ? $charset_map[$charset] : $charset;
        $server = empty($port) ? $host : "{$host},{$port}";
        $config = [
            'UID'      => $user,
            'PWD'      => $pwd,
            'Database' => $dbname,
        ];
        if ($charset != "UTF-8") {
            $config['CharacterSet'] = 'UTF-8';
        }
        $this->driver = new Driver($server, $config);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->driver = null;
    }

    /**
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return Driver
     */
    public function prototype()
    {
        return $this->driver;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $result = $this->driver->query($sql, $params);
        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                $result->fetchArray(function ($row) use (&$callback) {
                    $callback($row);
                });
                $result->freeStmt();
                return null;
            } else {
                $rows = [];
                $result->fetchArray(function ($row) use (&$rows) {
                    $rows[] = $row;
                });
                $result->freeStmt();
                return $rows;
            }
        } else {
            return $result->rowsAffected();
        }
    }

    /**
     * 开始事务
     */
    public function startTrans()
    {
        $this->driver->beginTransaction();
    }

    /**
     * 执行事务
     */
    public function commit()
    {
        $this->driver->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->driver->rollback();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在mssql中无效
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $sql = "SELECT @@IDENTITY";
        $result = $this->driver->query($sql);
        $result->fetch();
        $id = $result->getField(0);
        $result->freeStmt();
        return $id;
    }
}