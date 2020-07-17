<?php

namespace fize\database\extend\mysql\mode;

use Exception;
use mysqli as SysMysqli;
use fize\database\extend\mysql\Db;

/**
 * MySQLi
 *
 * MySQLi方式MySQL数据库模型类
 */
class Mysqli extends Db
{

    /**
     * @var SysMysqli 使用的mysqli对象
     */
    private $driver;

    /**
     * @var int|string 最后插入的自增ID或序列号
     */
    protected $lastInsertId = null;

    /**
     * 构造
     * @param string $host    服务器地址
     * @param string $user    用户名
     * @param string $pwd     用户密码
     * @param string $dbname  指定数据库
     * @param mixed  $port    端口号，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array  $opts    设置MYSQL连接选项
     * @param bool   $real    是否使用real方式，默认true
     * @param string $socket  指定应使用的套接字或命名管道，选填，默认不指定
     * @param array  $ssl_set 设置SSL选项，选填，为数组参数，其下有参数ENABLE、KEY、CERT、CA、CAPATH、CIPHER，如果ENABLE为true，则其余参数都需要填写
     * @param int    $flags   设置连接参数，选填，如MYSQLI_CLIENT_SSL等
     * @throws Exception
     */
    public function __construct($host, $user, $pwd, $dbname, $port = "", $charset = "utf8", array $opts = [], $real = true, $socket = null, array $ssl_set = [], $flags = null)
    {
        $port = (int)$port;  //mysqli有对类型进行了检查
        if ($real) {
            $this->driver = new SysMysqli();
            $this->driver->init();
            //real_connect之前只能使用options、ssl_set，其他方法无效
            foreach ($opts as $key => $value) {
                $this->driver->options($key, $value);
            }
            if (isset($ssl_set['ENABLE']) && $ssl_set['ENABLE'] == true) {
                $this->driver->ssl_set($ssl_set['KEY'], $ssl_set['CERT'], $ssl_set['CA'], $ssl_set['CAPATH'], $ssl_set['CIPHER']);
            }
            $this->driver->real_connect($host, $user, $pwd, $dbname, $port, $socket, $flags);
        } else {
            $this->driver = new SysMysqli($host, $user, $pwd, $dbname, $port, $socket);
        }

        if ($this->driver->connect_errno) {
            throw new Exception($this->driver->connect_error, $this->driver->connect_errno);
        }

        $this->driver->set_charset($charset);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $thread_id = $this->driver->thread_id;
        $this->driver->kill($thread_id);
        $this->driver->close();
        $this->driver = null;
    }

    /**
     * 多语句查询
     * @notice 非标准用法，不建议使用
     * @param array $querys 要进行查询的多条SQL语句组成的数组
     * @return array
     * @throws Exception
     */
    public function multiQuery(array $querys)
    {
        $sql = implode(";", $querys);
        if ($this->driver->multi_query($sql)) {
            $array_outer = [];
            do {
                $result = $this->driver->store_result();
                if ($result === false) {
                    throw new Exception($this->driver->connect_error, $this->driver->connect_errno);
                }
                $array_inner = [];
                while ($row = $result->fetch_assoc()) {
                    $array_inner[] = $row;
                }
                $result->free();
                $array_outer[] = $array_inner;
            } while ($this->driver->more_results() && $this->driver->next_result());
            return $array_outer;
        } else {
            throw new Exception($this->driver->connect_error, $this->driver->connect_errno);
        }
    }

    /**
     * 执行一个SQL查询
     * @param string   $sql      SQL语句，支持原生的mysqli问号占位符预处理
     * @param array    $params   可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则进行循环回调
     * @return array 返回结果数组
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $stmt = $this->driver->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->driver->error, $this->driver->errno);
        }

        if (!empty($params)) {
            $all_params = [];
            $vtypes = "";  // 数值类型
            foreach ($params as $val) {
                // 类型判断
                if (is_integer($val)) {
                    $vtypes .= "i";
                } elseif (is_double($val)) {
                    $vtypes .= "d";
                } elseif (is_object($val) || is_resource($val)) {
                    $vtypes .= "b";
                } else {
                    $vtypes .= "s";
                }
                $all_params[] = $val;
            }
            array_unshift($all_params, $vtypes);  // 插入数值类型
            $fun_params = [];
            foreach (array_keys($all_params) as $k) {
                $fun_params[] = &$all_params[$k];  // 注意此处的引用
            }
            call_user_func_array([$stmt, "bind_param"], $fun_params);  // 由于bind_param方法的参数个数不确定，目前方法以call_user_func_array解决
        }
        $result = $stmt->execute();

        if ($result === false) {
            throw new Exception($this->driver->connect_error, $this->driver->connect_errno);
        }

        //$meta = $stmt->result_metadata();
        $meta = $stmt->get_result();
        $rows = [];
        while ($row = $meta->fetch_assoc()) {
            if ($callback !== null) {
                $callback($row);
            }
            $rows[] = $row;
        }
        $meta->free();
        $stmt->close();
        return $rows;
    }

    /**
     * 执行一个SQL语句
     * @param string $sql    SQL语句，支持问号预处理语句
     * @param array  $params 可选的绑定参数
     * @return int 返回受影响行数
     */
    public function execute($sql, array $params = [])
    {
        $stmt = $this->driver->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->driver->error, $this->driver->errno);
        }

        if (!empty($params)) {
            $all_params = [];
            $vtypes = "";  // 数值类型
            foreach ($params as $val) {
                // 类型判断
                if (is_integer($val)) {
                    $vtypes .= "i";
                } elseif (is_double($val)) {
                    $vtypes .= "d";
                } elseif (is_object($val) || is_resource($val)) {
                    $vtypes .= "b";
                } else {
                    $vtypes .= "s";
                }
                $all_params[] = $val;
            }
            array_unshift($all_params, $vtypes);  // 插入数值类型
            $fun_params = [];
            foreach (array_keys($all_params) as $k) {
                $fun_params[] = &$all_params[$k];  // 注意此处的引用
            }
            call_user_func_array([$stmt, "bind_param"], $fun_params);  // 由于bind_param方法的参数个数不确定，目前方法以call_user_func_array解决
        }
        $result = $stmt->execute();

        if ($result === false) {
            throw new Exception($this->driver->connect_error, $this->driver->connect_errno);
        }

        if (stripos($sql, "INSERT") === 0 || stripos($sql, "REPLACE") === 0) {
            $this->lastInsertId = $stmt->insert_id;
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * 开始事务
     */
    public function startTrans()
    {
        $this->driver->begin_transaction();
    }

    /**
     * 执行事务
     */
    public function commit()
    {
        $this->driver->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->driver->rollback();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name 应该返回ID的那个序列对象的名称,该参数在mysql中无效
     * @return int|string
     */
    public function lastInsertId($name = null)
    {
        return $this->lastInsertId;
    }
}
