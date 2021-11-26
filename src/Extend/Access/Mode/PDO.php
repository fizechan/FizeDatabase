<?php

namespace Fize\Database\Extend\Access\Mode;

use Fize\Database\Exception\Exception;
use Fize\Database\Extend\Access\Db;
use Fize\Database\Middleware\PDO as Middleware;
use PDO as SysPDO;

/**
 * PDO
 *
 * (推荐使用)PDO方式ACCESS数据库模型
 */
class PDO extends Db
{
    use Middleware;

    /**
     * Pdo方式构造必须实例化$this->_pdo
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     */
    public function __construct(string $file, string $pwd = null, string $driver = null)
    {
        if (is_null($driver)) {
            $driver = "Microsoft Access Driver (*.mdb, *.accdb)";
        }
        $dsn = "odbc:Driver={" . $driver . "};DSN='';DBQ=" . realpath($file) . ";";
        if ($pwd) {
            $dsn .= "PWD={$pwd};";
        }
        $this->pdoConstruct($dsn, null, null);
    }

    /**
     * 析构时释放PDO资源
     */
    public function __destruct()
    {
        $this->pdoDestruct();
        parent::__destruct();
    }

    /**
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持原生的pdo问号预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query(string $sql, array $params = [], callable $callback = null): array
    {
        $sql = iconv('UTF-8', 'GBK', $sql);
        array_walk($params, function (&$value) {
            if (is_string($value)) {
                $value = iconv('UTF-8', 'GBK', $value);
            }
        });

        $stmt = $this->pdo->prepare($sql);

        if ($stmt === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception(iconv('GBK', 'UTF-8', $this->pdo->errorInfo()[2]), $this->pdo->errorCode());
        }

        if (!empty($params)) {
            $result = $stmt->execute($params); //绑定参数
        } else {
            $result = $stmt->execute();
        }

        if ($result === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception(iconv('GBK', 'UTF-8', $this->pdo->errorInfo()[2]), $this->pdo->errorCode());
        }

        $rows = [];
        while ($row = $stmt->fetch(SysPDO::FETCH_ASSOC, SysPDO::FETCH_ORI_NEXT)) {
            array_walk($row, function (&$value) {
                if (is_string($value)) {
                    $value = iconv('GBK', 'UTF-8', $value);
                }
            });
            if ($callback !== null) {
                $callback($row);
            }
            $rows[] = $row;
        }
        $stmt->closeCursor();
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
        $sql = iconv('UTF-8', 'GBK', $sql);
        array_walk($params, function (&$value) {
            if (is_string($value)) {
                $value = iconv('UTF-8', 'GBK', $value);
            }
        });

        $stmt = $this->pdo->prepare($sql);

        if ($stmt === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception(iconv('GBK', 'UTF-8', $this->pdo->errorInfo()[2]), $this->pdo->errorCode());
        }

        if (!empty($params)) {
            $result = $stmt->execute($params); //绑定参数
        } else {
            $result = $stmt->execute();
        }

        if ($result === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception(iconv('GBK', 'UTF-8', $this->pdo->errorInfo()[2]), $this->pdo->errorCode());
        }

        return $stmt->rowCount();
    }

    /**
     * 返回最后插入行的ID或序列值
     *
     * pdo连接odbc不支持lastInsertId方法，故使用原生查询获取
     * @param string|null $name 应该返回ID的那个序列对象的名称,该参数在access中无效
     * @return int|string
     */
    public function lastInsertId(string $name = null)
    {
        $stmt = $this->pdo->query("SELECT @@IDENTITY");
        $row = $stmt->fetch(SysPDO::FETCH_NUM, SysPDO::FETCH_ORI_NEXT);
        return $row[0];
    }
}
