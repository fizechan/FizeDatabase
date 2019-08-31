<?php


namespace fize\db\realization\mssql\mode;


use fize\db\realization\mssql\Db;
use fize\db\middleware\Odbc as Middleware;
use Exception;

/**
 * ODBC方式MSSQL数据库模型类
 * 注意ODBC返回的类型都为字符串格式(null除外)
 * 不需要额外的驱动和扩展支持，调用方便
 */
class Odbc extends Db
{
    use Middleware;

    /**
     * 构造
     * @param string $host 服务器地址，必填
     * @param string $user 用户名，必填
     * @param string $pwd 用户密码，必填
     * @param string $dbname 数据库名，必填
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param mixed $port 端口号，选填，MySQL默认是1344
     * @param string $driver 指定ODBC驱动名称。
     * @throws Exception
     */
    public function __construct($host, $user, $pwd, $dbname, $prefix = "", $port = "", $driver = null)
    {
        $this->tablePrefix = $prefix;
        if (is_null($driver)) {  //默认驱动名
            $driver = "{SQL Server}";
        }
        if(empty($port)){
            $server = $host;
        }else{
            $server = "{$host},{$port}";
        }
        $dsn = "Driver={$driver};Server={$server};Database={$dbname}";
        $this->odbcConstruct($dsn, $user, $pwd);
    }

    /**
     * 析构时释放ODBC资源
     */
    public function __destruct()
    {
        $this->odbcDestruct();
        parent::__destruct();
    }

    /**
     * 安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * ODBC为驱动层，安全化值应由各数据库自行实现
     * @param mixed $value
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
     * 对中文兼容性处理
     * @param string $string 待处理字符串
     * @param string $direction 方向 UTF8_2_GBK,GBK_2_UTF8
     * @return string 处理后字符串
     */
    private static function stringSerialize($string, $direction)
    {
        if ($direction == 'UTF8_2_GBK') {
            $string = iconv('UTF-8', 'GBK', $string);
        } else if ($direction == 'GBK_2_UTF8') {
            $string = iconv('GBK', 'UTF-8', $string);
        }
        return $string;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
     * @throws Exception
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = self::stringSerialize($sql, 'UTF8_2_GBK');
        array_walk($params, function(&$value){
            $value = self::stringSerialize($value, 'UTF8_2_GBK');
        });
        $this->driver->prepare($sql);
        $this->driver->execute($params); //绑定参数
        if (stripos($sql, "SELECT") === 0) {
            if ($callback !== null) {
                while ($assoc = $this->driver->fetchArray()) {
                    array_walk($assoc, function(&$value){
                        $value = self::stringSerialize($value, 'GBK_2_UTF8');
                    });
                    $callback($assoc);
                }
                $this->driver->freeResult();
                return null;
            } else {
                $rows = [];
                while ($row = $this->driver->fetchArray()) {
                    array_walk($row, function(&$value){
                        $value = self::stringSerialize($value, 'GBK_2_UTF8');
                    });
                    $rows[] = $row;
                }
                $this->driver->freeResult();
                return $rows; //返回数组
            }
        } else if(stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0){
            $this->driver->exec("SELECT @@IDENTITY");
            $id = $this->driver->result(1);
            return $id; //返回自增ID
        }else{
            $this->driver->exec("SELECT @@ROWCOUNT");
            $rows = $this->driver->result(1);
            return (int)$rows; //返回受影响条数
        }
    }
}