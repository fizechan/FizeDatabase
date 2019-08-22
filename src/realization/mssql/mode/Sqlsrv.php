<?php


namespace fize\db\realization\mssql\mode;

use fize\db\realization\mssql\Db;
use fize\db\realization\mssql\mode\driver\Sqlsrv as Driver;


/**
 * MSSQL的ORM模型
 * php_sqlsrv.dll需要本地客户端支持，不同版本使用的客户端不同，可以在错误信息中获取相关资料。
 * php_sqlsrv.dll由微软官方提供技术支持，推荐使用。
 */
class Sqlsrv extends Db
{

    /**
     * 使用的MSSQL对象
     * @var Driver
     */
    private $_driver = null;

    /**
     * 构造
     * @param string $host 数据库服务器
     * @param string $user 数据库登录账户
     * @param string $pwd 数据库登录密码
     * @param string $dbname 数据库名
     * @param string $prefix 指定前缀，选填，默认空字符
     * @param mixed $port 数据库服务器端口，选填，默认是1433(默认设置)
     * @param string $charset 指定数据库编码，默认GBK,(不区分大小写)
     */
    public function __construct($host, $user, $pwd, $dbname, $prefix = "", $port = "", $charset = "GBK")
    {
        $charset = strtoupper($charset);
        $charset_map = [
            'UTF8' => 'UTF-8',
        ];
        $charset = isset($charset_map[$charset]) ? $charset_map[$charset] : $charset;
        $server = empty($port) ? $host : "{$host},{$port}";
        if ($charset != "UTF-8") {
            $config = [
                'UID'          => $user,
                'PWD'          => $pwd,
                'CharacterSet' => 'UTF-8',
                'Database'     => $dbname,
            ];
        } else {
            $config = [
                'UID'      => $user,
                'PWD'      => $pwd,
                'Database' => $dbname,
            ];
        }
        $this->_tablePrefix = $prefix;
        $this->_driver = new Driver($server, $config);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->_driver->close();
    }

    /**
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return Driver
     */
    public function prototype()
    {
        return $this->_driver;
    }

    /**
     * 自己实现的安全化值
     * @param mixed $value 要安全化的值
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . str_replace("'", "''", $value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'NULL';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组(错误返回false)，INSERT/REPLACE返回自增ID，其余返回受影响行数
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $rst = $this->_driver->query($sql, $params);
        if (!$rst) {
            return false;
        }
        if (stripos($sql, "INSERT") === 0) {
            //获取最后的自增ID
            $id_sql = "SELECT @@IDENTITY";
            $this->_driver->query($id_sql);
            $this->_driver->fetch();
            $id = $this->_driver->getField(0);
            $this->_driver->freeStmt();
            return $id;
        } elseif (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                $this->_driver->fetchArray(function ($row) use (&$callback) {
                    $callback($row);  //循环回调
                });
                $this->_driver->freeStmt();
                return null;
            } else {
                $rows = [];
                $this->_driver->fetchArray(function ($row) use (&$rows) {
                    $rows[] = $row;
                });
                $this->_driver->freeStmt();
                return $rows;  //返回结果数组
            }
        } else {
            return $this->_driver->rowsAffected(); //返回受影响条数
        }
    }

    /**
     * 开始事务
     * @return void
     */
    public function startTrans()
    {
        $this->_driver->beginTransaction();
    }

    /**
     * 执行事务
     * @return void
     */
    public function commit()
    {
        $this->_driver->commit();
    }

    /**
     * 回滚事务
     * @return void
     */
    public function rollback()
    {
        $this->_driver->rollback();
    }
}