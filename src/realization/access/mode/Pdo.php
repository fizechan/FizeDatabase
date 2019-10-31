<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace fize\db\realization\access\mode;

use fize\db\realization\access\Db;
use fize\db\middleware\Pdo as Middleware;
use PDO as Driver;
use fize\db\exception\Exception;


/**
 * PDO方式(推荐使用)ACCESS数据库模型类
 */
class Pdo extends Db
{
    use Middleware;

    /**
     * Pdo方式构造必须实例化$this->_pdo
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $driver 指定ODBC驱动名称。
     */
    public function __construct($file, $pwd = null, $driver = null)
    {
        if (is_null($driver)) {
            $driver = "Microsoft Access Driver (*.mdb, *.accdb)";
        }
        $dsn = "odbc:Driver={" . $driver . "};DSN='';DBQ=" . realpath($file) . ";";
        if($pwd) {
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
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int SELECT语句返回数组，其余返回受影响行数。
     * @throws Exception
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = iconv('UTF-8', 'GBK', $sql);
        array_walk($params, function(&$value){
            if(is_string($value)) {
                $value = iconv('UTF-8', 'GBK', $value);
            }
        });

        $stmt = $this->pdo->prepare($sql);

        if ($stmt === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception( iconv('GBK', 'UTF-8', $this->pdo->errorInfo()[2]), $this->pdo->errorCode());
        }

        if (!empty($params)) {
            $result = $stmt->execute($params); //绑定参数
        } else {
            $result = $stmt->execute();
        }

        if ($result === false) {
            //0为数据库错误代码，1为驱动错误代码，2为错误描述
            throw new Exception( iconv('GBK', 'UTF-8', $this->pdo->errorInfo()[2]), $this->pdo->errorCode());
        }

        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($row = $stmt->fetch(Driver::FETCH_ASSOC, Driver::FETCH_ORI_NEXT)) {
                    array_walk($row, function(&$value){
                        if(is_string($value)) {
                            $value = iconv('GBK', 'UTF-8', $value);
                        }
                    });
                    $callback($row);
                }
                $stmt->closeCursor();
                return null;
            } else {
                $rows = [];
                while ($row = $stmt->fetch(Driver::FETCH_ASSOC, Driver::FETCH_ORI_NEXT)) {
                    array_walk($row, function(&$value){
                        if(is_string($value)) {
                            $value = iconv('GBK', 'UTF-8', $value);
                        }
                    });
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
     * 返回最后插入行的ID或序列值
     * pdo连接odbc不支持lastInsertId方法，故使用原生查询获取
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在access中无效
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        $stmt = $this->pdo->query("SELECT @@IDENTITY");
        $row = $stmt->fetch(Driver::FETCH_NUM, Driver::FETCH_ORI_NEXT);
        return $row[0];
    }
}