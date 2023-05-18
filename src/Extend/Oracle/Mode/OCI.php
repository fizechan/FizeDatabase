<?php

namespace Fize\Database\Extend\Oracle\Mode;

use Fize\Database\Driver\OCI\OCI as Driver;
use Fize\Database\Extend\Oracle\Db;

/**
 * Oci
 *
 * Oci方式Oracle数据库模型类
 */
class OCI extends Db
{

    /**
     * @var Driver 使用的OCI对象
     */
    protected $driver = null;

    /**
     * @var int 执行模式
     */
    protected $mode = null;

    /**
     * 初始化
     * @param string      $username          用户名
     * @param string      $password          密码
     * @param string|null $connection_string 连接串
     * @param string|null $character_set     编码
     * @param int|null    $session_mode      会话模式
     * @param int         $connect_type      连接模式
     */
    public function __construct(string $username, string $password, string $connection_string = null, string $character_set = null, int $session_mode = null, int $connect_type = 1)
    {
        $this->driver = new Driver($username, $password, $connection_string, $character_set, $session_mode, $connect_type);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->driver = null;
    }

    /**
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持原生的:value预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query(string $sql, array $params = [], callable $callback = null): array
    {
        if ($params) {  //将?占位符还原为:$*占位符以方便处理
            $parts = explode('?', $sql);
            $temp_sql = $parts[0];
            for ($i = 1; $i < count($parts); $i++) {
                $temp_sql .= ":$" . $i . $parts[$i];
            }
            $sql = $temp_sql;
        }

        $stmt = $this->driver->parse($sql);
        if ($params) {
            $index = 0;
            foreach ($params as $value) {
                $index++;
                $stmt->bindByName(":${$index}", $value);
            }
        }
        $stmt->execute($this->mode);
        $rows = [];
        while ($row = $stmt->fetchAssoc()) {
            if ($callback !== null) {
                $callback($row);
            }
            $rows[] = $row;
        }
        $stmt->freeStatement();
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
        if ($params) {  //将?占位符还原为:$*占位符以方便处理
            $parts = explode('?', $sql);
            $temp_sql = $parts[0];
            for ($i = 1; $i < count($parts); $i++) {
                $temp_sql .= ":$" . $i . $parts[$i];
            }
            $sql = $temp_sql;
        }

        $stmt = $this->driver->parse($sql);
        if ($params) {
            $index = 0;
            foreach ($params as $value) {
                $index++;
                $stmt->bindByName(":${$index}", $value);
            }
        }
        $stmt->execute($this->mode);
        return $stmt->numRows();
    }

    /**
     * 开始事务
     */
    public function startTrans()
    {
        $this->mode = OCI_NO_AUTO_COMMIT;
    }

    /**
     * 执行事务
     */
    public function commit()
    {
        $this->driver->commit();
        $this->mode = OCI_COMMIT_ON_SUCCESS;
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->driver->rollback();
        $this->mode = OCI_COMMIT_ON_SUCCESS;
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在oracle中必须指定
     * @return int|string
     */
    public function lastInsertId(string $name = null)
    {
        $sql = "SELECT {$name}.currval FROM dual";
        $stmt = $this->driver->parse($sql);
        $stmt->execute($this->mode);
        $row = $stmt->fetchArray(OCI_NUM);
        return $row[0];
    }
}
