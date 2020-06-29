<?php

namespace fize\database\middleware;

use PDO as SysPDO;
use PDOException;
use fize\database\exception\Exception;

/**
 * PDO
 */
trait Pdo
{
    /**
     * @var SysPDO 使用的PDO对象
     */
    private $pdo = null;

    /**
     * 构造PDO
     * @param string $dsn  DSN字符串
     * @param string $user 用户名
     * @param string $pwd  密码
     * @param array  $opts 可选的选项
     */
    protected function pdoConstruct($dsn, $user, $pwd, array $opts = [])
    {
        if (!empty($opts)) {
            $this->pdo = new SysPDO($dsn, $user, $pwd, $opts);
        } else {
            $this->pdo = new SysPDO($dsn, $user, $pwd);
        }
        $this->pdo->setAttribute(SysPDO::ATTR_ERRMODE, SysPDO::ERRMODE_EXCEPTION);
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
     * @return SysPDO
     */
    public function prototype()
    {
        return $this->pdo;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string   $sql      SQL语句，支持原生的pdo问号预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params); //绑定参数
            } else {
                $stmt->execute();
            }
            $rows = [];
            while ($row = $stmt->fetch(SysPDO::FETCH_ASSOC, SysPDO::FETCH_ORI_NEXT)) {
                $rows[] = $row;
                if ($callback !== null) {
                    $callback($row);
                }
            }
            $stmt->closeCursor();
            return $rows;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $this->getLastSql(true));
        }
    }

    /**
     * 执行一个SQL语句
     * @param string $sql    SQL语句，支持问号预处理语句
     * @param array  $params 可选的绑定参数
     * @return int 返回受影响行数
     */
    public function execute($sql, array $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params); //绑定参数
            } else {
                $stmt->execute();
            }
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $this->getLastSql(true));
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
