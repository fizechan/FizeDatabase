<?php


namespace fize\db\realization\mysql\mode;


use fize\db\realization\mysql\Db;
use mysqli as Driver;
use Exception;

/**
 * MySQLi数据库模型类
 */
class Mysqli extends Db
{

    /**
     * 使用的mysqli对象
     * @var Driver
     */
    private $_driver = null;

    /**
     * 构造
     * @param string $host 服务器地址
     * @param string $user 用户名
     * @param string $pwd 用户密码
     * @param string $dbname 指定数据库
     * @param string $prefix 指定全局前缀
     * @param mixed $port 端口号，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array $opts 设置MYSQL连接选项
     * @param bool $real 是否使用real方式，默认true
     * @param string $socket 指定应使用的套接字或命名管道，选填，默认不指定
     * @param array $ssl_set 设置SSL选项，选填，为数组参数，其下有参数ENABLE、KEY、CERT、CA、CAPATH、CIPHER，如果ENABLE为true，则其余参数都需要填写
     * @param int $flags 设置连接参数，选填，如MYSQLI_CLIENT_SSL等
     * @throws Exception
     */
    public function __construct($host, $user, $pwd, $dbname, $prefix = "", $port = "", $charset = "utf8", array $opts = [], $real = true, $socket = null, array $ssl_set = [], $flags = null)
    {
        $this->_tablePrefix = $prefix;
        $port = (int)$port;  //mysqli有对类型进行了检查
        if ($real) {
            $this->_driver = new Driver();
            $this->_driver->init();
            //real_connect之前只能使用options、ssl_set，其他方法无效
            foreach ($opts as $key => $value) {
                $this->_driver->options($key, $value);
            }
            if (isset($ssl_set['ENABLE']) && $ssl_set['ENABLE'] == true) {
                $this->_driver->ssl_set($ssl_set['KEY'], $ssl_set['CERT'], $ssl_set['CA'], $ssl_set['CAPATH'], $ssl_set['CIPHER']);
            }
            $this->_driver->real_connect($host, $user, $pwd, $dbname, $port, $socket, $flags);
        } else {
            $this->_driver = new Driver($host, $user, $pwd, $dbname, $port, $socket);
        }

        if ($this->_driver->connect_errno) {
            throw new Exception($this->_driver->connect_error, $this->_driver->connect_errno);
        }

        $this->_driver->set_charset($charset);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $thread_id = $this->_driver->thread_id;
        $this->_driver->kill($thread_id);
        $this->_driver->close();
        $this->_driver = null;
    }

    /**
     * 返回当前使用的数据库对象原型，用于原生操作
     * @return Driver
     */
    public function prototype()
    {
        return $this->_driver;
    }

    /**
     * 多语句查询
     * @todo 非标准用法，请勿使用
     * @param array $querys 要进行查询的多条SQL语句组成的数组
     * @return array
     * @throws Exception
     */
    public function multiQuery(array $querys)
    {
        $sql = implode(";", $querys);
        if ($this->_driver->multi_query($sql)) {
            $array_outer = [];
            do {
                $result = $this->_driver->store_result();
                if($result === false){
                    throw new Exception($this->_driver->connect_error, $this->_driver->connect_errno);
                }
                $array_inner = [];
                while ($row = $result->fetch_assoc()) {
                    $array_inner[] = $row;
                }
                $result->free();
                $array_outer[] = $array_inner;
            } while ($this->_driver->more_results() && $this->_driver->next_result());
            return $array_outer;
        } else {
            throw new Exception($this->_driver->connect_error, $this->_driver->connect_errno);
        }
    }

    /**
     * mysqli函数实现的安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . $this->_driver->escape_string($value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的mysqli问号占位符预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return mixed SELECT语句返回数组($callback有定义时不返回)，INSERT/REPLACE返回自增ID，其余返回受影响行数
     * @throws Exception
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $stmt = $this->_driver->prepare($sql);

        if(!$stmt){
            throw new Exception($this->_driver->connect_error, $this->_driver->connect_errno);
        }

        if (!empty($params)) {
            $all_params = [];
            $vtypes = ""; //数值类型
            foreach ($params as $val) {
                //类型判断
                if (is_integer($val)) {
                    $vtypes .= "i";
                } elseif (is_double($val)) {
                    $vtypes .= "d";
                } elseif (is_object($val) || is_resource($val)) {
                    $vtypes .= "b";
                } else {
                    $vtypes .= "s";
                }
                $all_params[] = &$val;
            }
            array_unshift($all_params, $vtypes); //插入数值类型
            $fun_params = [];
            foreach (array_keys($all_params) as $k) {
                $fun_params[] = &$all_params[$k]; //注意此处的引用
            }
            call_user_func_array([$stmt, "bind_param"], $fun_params); //由于bind_param方法的参数个数不确定，目前方法以call_user_func_array解决
        }
        $result = $stmt->execute();

        if($result === false){
            throw new Exception($this->_driver->connect_error, $this->_driver->connect_errno);
        }

        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id; //返回自增ID
        } elseif (stripos($sql, "SELECT") === 0) {
            //$meta = $stmt->result_metadata();
            $meta = $stmt->get_result();
            $out = [];
            if ($callback !== null) {
                while ($assoc = $meta->fetch_assoc()) {
                    $callback($assoc);
                }
                $meta->free();
                $stmt->close();
                return null;
            } else {
                while ($assoc = $meta->fetch_assoc()) {
                    $out[] = $assoc;
                }
                $meta->free();
                $stmt->close();
                return $out; //返回数组
            }
        } else {
            $rows = $stmt->affected_rows;
            $stmt->close();
            return $rows; //返回受影响条数
        }
    }

    /**
     * 开始事务
     * @return void
     */
    public function startTrans()
    {
        $this->_driver->begin_transaction();
    }

    /**
     * 执行事务
     * @return void
     */
    public function commit()
    {
        $this->_driver->commit();
    }

    /**
     * 回滚事务
     * @return void
     */
    public function rollback()
    {
        $this->_driver->rollback();
    }
}