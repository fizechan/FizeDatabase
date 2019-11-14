<?php
/** @noinspection PhpComposerExtensionStubsInspection */


namespace fize\db\middleware;

use PDO as Driver;
use fize\db\exception\Exception;

/**
 * PDO
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
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     * @throws Exception
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $stmt = $this->pdo->prepare($sql);

        if ($stmt === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            //var_dump($this->pdo->errorInfo());
            //throw new Exception($this->pdo->errorCode() . ":" . iconv('GBK', 'UTF-8', $this->pdo->errorInfo()[2]));
            throw new Exception($this->pdo->errorCode() . ":" . $this->pdo->errorInfo()[2]);
        }

        if (!empty($params)) {
            $result = $stmt->execute($params); //绑定参数
        } else {
            $result = $stmt->execute();
        }

        if ($result === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception($this->pdo->errorInfo()[2], $this->pdo->errorCode());
        }

        if (stripos($sql, "SELECT") === 0) {
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
                return $rows;
            }
        } else {
            return $stmt->rowCount();
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

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        return $this->pdo->lastInsertId($name);
    }
}