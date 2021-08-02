<?php

namespace fize\database\middleware;

use fize\database\driver\Odbc as SysOdbc;

/**
 * ODBC
 * @todo ODBC返回的类型都为字符串格式(null除外)，应进行统一处理
 */
trait Odbc
{
    /**
     * 使用的ODBC对象
     * @var SysOdbc
     */
    protected $driver = null;

    /**
     * 构建ODBC
     * @see https://www.connectionstrings.com/
     * @param string   $dsn         连接的数据库源名称。另外，一个无DSN连接字符串可以使用。
     * @param string   $user        用户名
     * @param string   $pwd         密码
     * @param int|null $cursor_type 可选SQL_CUR_USE_IF_NEEDED | SQL_CUR_USE_ODBC | SQL_CUR_USE_DRIVER
     * @param bool     $pconnect    是否使用长链接，默认false
     */
    protected function odbcConstruct(string $dsn, string $user, string $pwd, int $cursor_type = null, bool $pconnect = false)
    {
        $this->driver = new SysOdbc($dsn, $user, $pwd, $cursor_type, $pconnect);
    }

    /**
     * 析构函数
     */
    protected function odbcDestruct()
    {
        $this->driver->close();
    }

    /**
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持原生的ODBC问号预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query(string $sql, array $params = [], callable $callback = null): array
    {
        $result = $this->driver->prepare($sql);
        $result->execute($params);
        $rows = [];
        while ($row = $result->fetchArray()) {
            $rows[] = $row;
            if ($callback !== null) {
                $callback($row);
            }
        }
        $result->freeResult();
        return $rows;
    }

    /**
     * 执行一个SQL语句
     * @param string $sql    SQL语句，支持问号预处理语句
     * @param array  $params 可选的绑定参数
     * @return int 返回受影响行数
     */
    public function execute(string $sql, array $params = []): int
    {
        $result = $this->driver->prepare($sql);
        $result->execute($params);
        return $result->numRows();
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
