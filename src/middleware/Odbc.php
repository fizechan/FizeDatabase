<?php


namespace fize\db\middleware;


use fize\db\middleware\driver\Odbc as Driver;

/**
 * ODBC中间层
 * @todo ODBC返回的类型都为字符串格式(null除外)，应进行统一处理
 */
trait Odbc
{
    /**
     * 使用的ODBC对象
     * @var Driver
     */
    protected $driver = null;

    /**
     * 构建ODBC
     * @see https://www.connectionstrings.com/
     * @param string $dsn 连接的数据库源名称。另外，一个无DSN连接字符串可以使用。
     * @param string $user 用户名
     * @param string $pwd 密码
     * @param int $cursor_type 可选SQL_CUR_USE_IF_NEEDED | SQL_CUR_USE_ODBC | SQL_CUR_USE_DRIVER
     * @param bool $pconnect 是否使用长链接，默认false
     */
    protected function odbcConstruct($dsn, $user, $pwd, $cursor_type = null, $pconnect = false)
    {
        $this->driver = new Driver($dsn, $user, $pwd, $cursor_type, $pconnect);
    }

    /**
     * 析构函数
     */
    protected function odbcDestruct()
    {
        $this->driver->close();
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
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $result = $this->driver->prepare($sql);
        $result->execute($params);
        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($assoc = $result->fetchArray()) {
                    $callback($assoc);
                }
                $result->freeResult();
                return null;
            } else {
                $rows = [];
                while ($row = $result->fetchArray()) {
                    $rows[] = $row;
                }
                $result->freeResult();
                return $rows;
            }
        } else {
            return $result->numRows();
        }
    }

    /**
     * 开始事务
     */
    public function startTrans()
    {
        $this->driver->autocommit(false);
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
}