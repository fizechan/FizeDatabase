<?php

namespace fize\database\driver;

use Exception;
use OCI_Collection;
use OCI_Lob;
use fize\database\driver\oci\Statement;

/**
 * Oci驱动
 *
 * 需要启用扩展ext-oci8
 */
class Oci
{

    /**
     * 连接类型：默认
     */
    const CONNECT_TYPE_DEFALUT = 1;

    /**
     * 连接类型：新连接
     */
    const CONNECT_TYPE_NEW = 2;

    /**
     * 连接类型：长连接
     */
    const CONNECT_TYPE_PERSISTENT = 3;

    /**
     * @var resource Oracle连接
     */
    protected $connection;

    /**
     * @var resource 游标
     */
    protected $cursor;

    /**
     * 构造，创建连接
     * @param string $username          用户名
     * @param string $password          密码
     * @param string $connection_string 连接串
     * @param string $character_set     编码
     * @param int    $session_mode      会话模式
     * @param int    $connect_type      连接模式
     */
    public function __construct($username, $password, $connection_string = null, $character_set = null, $session_mode = null, $connect_type = 1)
    {
        switch ($connect_type) {
            case self::CONNECT_TYPE_DEFALUT:
                $this->connect($username, $password, $connection_string, $character_set, $session_mode);
                break;
            case self::CONNECT_TYPE_NEW:
                $this->newConnect($username, $password, $connection_string, $character_set, $session_mode);
                break;
            case self::CONNECT_TYPE_PERSISTENT:
                $this->pconnect($username, $password, $connection_string, $character_set, $session_mode);
                break;
        }
    }

    /**
     * 析构时关闭连接
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 返回Oracle客户端库版本
     * @return string
     */
    public static function clientVersion()
    {
        return oci_client_version();
    }

    /**
     * 关闭 Oracle 连接
     * @return bool
     */
    public function close()
    {
        if (!$this->connection) {
            return true;
        }
        if ($this->connection) {
            $result = oci_close($this->connection);
            if ($result) {
                $this->connection = null;
            }
            return $result;
        }
        return true;
    }

    /**
     * 提交未执行的事务处理
     * @return bool
     */
    public function commit()
    {
        return oci_commit($this->connection);
    }

    /**
     * 建立一个到 Oracle 服务器的连接
     * @param string $username          用户名
     * @param string $password          密码
     * @param string $connection_string 连接串
     * @param string $character_set     编码
     * @param int    $session_mode      会话模式
     * @throws Exception
     */
    public function connect($username, $password, $connection_string = null, $character_set = null, $session_mode = null)
    {
        if ($this->connection) {
            $this->close();
        }
        $connection = oci_connect($username, $password, $connection_string, $character_set, $session_mode);
        if (!$connection) {
            $err = $this->error();
            throw new Exception($err['message'], $err['code']);
        }
        $this->connection = $connection;
    }

    /**
     * 返回上一个错误
     * @return array
     */
    public function error()
    {
        if (is_null($this->connection)) {
            return oci_error();
        }
        return oci_error($this->connection);
    }

    /**
     * 释放描述符
     * @param OCI_Lob|resource $descriptor 描述符
     * @return bool
     */
    public static function freeDescriptor($descriptor)
    {
        return oci_free_descriptor($descriptor);
    }

    /**
     * 打开或关闭内部调试输出
     * @param int $onoff 设置 onoff 为 0 关闭调试输出，为 1 则打开。
     */
    public static function internalDebug($onoff)
    {
        oci_internal_debug($onoff);
    }

    /**
     * 复制大对象副本
     * @param OCI_Lob $lob_to   接受复制值的对象
     * @param OCI_Lob $lob_from 被复制的对象
     * @param int     $length   指示要复制的数据的长度。
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE
     * @todo 测试未通过
     */
    public static function lobCopy($lob_to, $lob_from, $length = 0)
    {
        return oci_lob_copy($lob_to, $lob_from, $length);
    }

    /**
     * 判断两个大对象副本是否相等
     * @param OCI_Lob $lob1 对象1
     * @param OCI_Lob $lob2 对象2
     * @return bool
     * @todo 测试未通过
     */
    public static function lobIsEqual(OCI_Lob $lob1, OCI_Lob $lob2)
    {
        return oci_lob_is_equal($lob1, $lob2);
    }

    /**
     * 分配新的 collection 对象
     * @param string $tdo    有效的名字类型（大写）。
     * @param string $schema 指向建立名字对象的架构
     * @return OCI_Collection 出错时返回false
     */
    public function newCollection($tdo, $schema = null)
    {
        return oci_new_collection($this->connection, $tdo, $schema);
    }

    /**
     * 建定一个到 Oracle 服务器的新连接
     * @param string $username          用户名
     * @param string $password          密码
     * @param string $connection_string 连接串
     * @param string $character_set     编码
     * @param int    $session_mode      会话模式
     */
    public function newConnect($username, $password, $connection_string = null, $character_set = null, $session_mode = null)
    {
        if ($this->connection) {
            $this->close();
        }
        $connection = oci_new_connect($username, $password, $connection_string, $character_set, $session_mode);
        if (!$connection) {
            $err = $this->error();
            throw new Exception($err['message'], $err['code']);
        }
        $this->connection = $connection;
    }

    /**
     * 分配并返回一个新的游标
     * @return Statement 返回预处理对象
     */
    public function newCursor()
    {
        $cursor = oci_new_cursor($this->connection);
        return new Statement($cursor);
    }

    /**
     * 初始化一个新的空 LOB 或 FILE 描述符
     * @param int $type 类型
     * @return OCI_Lob|resource
     */
    public function newDescriptor($type = 50)
    {
        return oci_new_descriptor($this->connection, $type);
    }

    /**
     * 配置 Oracle 语句预备执行
     * @param string $query SQL语句
     * @return Statement 返回预处理对象
     */
    public function parse($query)
    {
        $statement = oci_parse($this->connection, $query);
        return new Statement($statement);
    }

    /**
     * 修改 Oracle 用户的密码
     * @param string $username     用户名
     * @param string $old_password 原密码
     * @param string $new_password 新密码
     * @return bool
     */
    public function passwordChange($username, $old_password, $new_password)
    {
        return oci_password_change($this->connection, $username, $old_password, $new_password);
    }

    /**
     * 使用一个持久连接连到 Oracle 数据库
     * @param string $username          用户名
     * @param string $password          密码
     * @param string $connection_string 连接串
     * @param string $character_set     编码
     * @param int    $session_mode      会话模式
     */
    public function pconnect($username, $password, $connection_string = null, $character_set = null, $session_mode = null)
    {
        if ($this->connection) {
            $this->close();
        }
        $connection = oci_pconnect($username, $password, $connection_string, $character_set, $session_mode);
        if (!$connection) {
            $err = $this->error();
            throw new Exception($err['message'], $err['code']);
        }
        $this->connection = $connection;
    }

    /**
     * 为Oracle数据库TAF注册一个用户定义的回调函数
     * @param mixed $callback_fn 回调函数名或者回调函数体
     * @return bool
     * @since PHP7.2
     */
    public function registerTafCallback($callback_fn)
    {
        return oci_register_taf_callback($this->connection, $callback_fn);
    }

    /**
     * 回滚未提交的事务
     * @return bool
     */
    public function rollback()
    {
        return oci_rollback($this->connection);
    }

    /**
     * 返回服务器版本信息
     * @return string
     */
    public function serverVersion()
    {
        return oci_server_version($this->connection);
    }

    /**
     * 设置动作名称
     * @param string $action_name 动作名
     * @return bool
     */
    public function setAction($action_name)
    {
        return oci_set_action($this->connection, $action_name);
    }

    /**
     * 设置数据库调用的毫秒超时
     * @param int $time_out 毫秒数
     * @return mixed
     * @since Oracle 18以上版本才支持该方法
     */
    public function setCallTimeout($time_out)
    {
        return oci_set_call_timeout($this->connection, $time_out);
    }

    /**
     * 设置客户端标识符
     * @param string $client_identifier 标识符
     * @return bool
     */
    public function setClientIdentifier($client_identifier)
    {
        return oci_set_client_identifier($this->connection, $client_identifier);
    }

    /**
     * 设置客户端信息
     * @param string $client_info 客户端信息
     * @return bool
     */
    public function setClientInfo($client_info)
    {
        return oci_set_client_info($this->connection, $client_info);
    }

    /**
     * 设置数据库操作
     * @param string $dbop 数据库操作
     * @return bool
     * @since Oracle 12以上版本才支持该方法
     */
    public function setDbOperation($dbop)
    {
        return oci_set_db_operation($this->connection, $dbop);
    }

    /**
     * 设置数据库版本
     * @param string $edition 版本
     * @return bool
     */
    public static function setEdition($edition)
    {
        return oci_set_edition($edition);
    }

    /**
     * 设置模块名称
     * @param $module_name
     * @return bool
     */
    public function setModuleName($module_name)
    {
        return oci_set_module_name($this->connection, $module_name);
    }

    /**
     * 取消注册Oracle数据库TAF的用户定义回调函数
     * @return bool
     * @since PHP7.2
     */
    public function unregisterTafCallback()
    {
        return oci_unregister_taf_callback($this->connection);
    }
}
