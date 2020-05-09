<?php

namespace fize\db\extend\oracle\mode;

use fize\db\driver\Oci as Driver;
use fize\db\extend\oracle\Db;

/**
 * Oci
 *
 * Oci方式Oracle数据库模型类
 */
class Oci extends Db
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
     * @param string $username          用户名
     * @param string $password          密码
     * @param string $connection_string 连接串
     * @param string $character_set     编码
     * @param int    $session_mode      会话模式
     * @param int    $connect_type      连接模式
     */
    public function __construct($username, $password, $connection_string = null, $character_set = null, $session_mode = null, $connect_type = 1)
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
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return Driver
     */
    public function prototype()
    {
        return $this->driver;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string   $sql      SQL语句，支持原生的:value预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
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
        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($row = $stmt->fetchAssoc()) {
                    $callback($row);
                }
                $stmt->freeStatement();
                return null;
            } else {
                $rows = [];
                while ($row = $stmt->fetchAssoc()) {
                    $rows[] = $row;
                }
                $stmt->freeStatement();
                return $rows;
            }
        } else {
            return $stmt->numRows();
        }
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
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在oracle中必须指定
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $sql = "SELECT {$name}.currval FROM dual";
        $stmt = $this->driver->parse($sql);
        $stmt->execute($this->mode);
        $row = $stmt->fetchArray(OCI_NUM);
        return $row[0];
    }
}
