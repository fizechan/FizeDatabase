<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\realization\oracle\mode;

use fize\db\realization\oracle\Db;
use fize\db\realization\oracle\mode\driver\Oci as Driver;

/**
 * Oci方式Oracle数据库模型类
 * @package fize\db\realization\oracle\mode
 */
class Oci extends Db
{

    /**
     * 使用的OCI对象
     * @var Driver
     */
    protected $driver = null;

    /**
     * @var null 执行模式
     */
    protected $mode = null;

    /**
     * 初始化
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $connection_string 连接串
     * @param string $character_set 编码
     * @param int $session_mode 会话模式
     * @param int $connect_type 连接模式
     */
    public function __construct($username, $password, $connection_string = null, $character_set = null, $session_mode = null, $connect_type = 1)
    {
        $this->driver = new Driver($username, $password, $connection_string, $character_set, $session_mode, $connect_type );
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
     * 安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * @param mixed $value 要安全化的值
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . addcslashes($value, "'") . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的:value预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int|null SELECT语句返回数组，INSERT/REPLACE返回自增ID，其余返回受影响行数。
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $stmt = $this->driver->parse($sql);
        if($params) {
            foreach ($params as $name => $value) {
                $stmt->bindByName(":{$name}", $value);
            }
        }
        $rst = $stmt->execute($this->mode);
        if (!$rst) {
            return false;
        }
        if (stripos($sql, "INSERT") === 0) {
            //获取最后的自增ID
            return 0;
        } elseif (stripos($sql, "SELECT") === 0) {
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
                return $rows;  //返回结果数组
            }
        } else {
            return $stmt->numRows(); //返回受影响条数
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
}