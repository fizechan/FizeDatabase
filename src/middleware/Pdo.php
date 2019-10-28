<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\middleware;

use PDO as Driver;
use fize\db\exception\Exception;

/**
 * PDO中间层
 */
trait Pdo
{
    /**
     * 使用的PDO对象
     * @var Driver
     */
    private $pdo = null;

    /**
     * 构造PDO
     * @param string $dsn DSN字符串
     * @param string $user 用户名
     * @param string $pwd 密码
     * @param array $opts 可选的选项
     */
    protected function pdoConstruct($dsn, $user, $pwd, array $opts = [])
    {
        if (!empty($opts)) {
            $this->pdo = new Driver($dsn, $user, $pwd, $opts);
        } else {
            $this->pdo = new Driver($dsn, $user, $pwd);
        }
    }

    /**
     * 析构函数
     */
    protected function pdoDestruct()
    {
        $this->pdo = null;
    }

    /**
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return Driver
     */
    public function prototype()
    {
        return $this->pdo;
    }

    /**
     * PDO实现的安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = $this->pdo->quote($value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
     * @throws Exception
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception($this->pdo->errorCode() . ":" . $this->pdo->errorInfo()[2]);
        }

        if (!empty($params)) {
            $result = $stmt->execute($params); //绑定参数
        } else {
            $result = $stmt->execute();
        }

        if (!$result) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            //var_dump($this->_pdo->errorInfo());
            //var_dump($this->_pdo->errorCode());
            throw new Exception($this->pdo->errorInfo()[2], $this->pdo->errorCode());
        }

        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
            $id = $this->pdo->lastInsertId(); //@todo lastInsertId支持name参数
            return $id; //返回自增ID
        } elseif (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($row = $stmt->fetch(Driver::FETCH_ASSOC, Driver::FETCH_ORI_NEXT)) {
                    $callback($row);
                }
                $stmt->closeCursor();
                return null;
            } else {
                $rows = [];
                while ($row = $stmt->fetch(Driver::FETCH_ASSOC, Driver::FETCH_ORI_NEXT)) {
                    $rows[] = $row;
                }
                $stmt->closeCursor();
                return $rows; //返回数组
            }
        } else {
            $num = $stmt->rowCount();
            return $num; //返回受影响条数
        }
    }

    /**
     * 开始事务
     */
    public function startTrans()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * 执行事务
     */
    public function commit()
    {
        $this->pdo->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->pdo->rollBack();
    }
}