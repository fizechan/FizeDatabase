<?php

namespace fize\db\definition\db;

/**
 * 数据库模型抽象类基本功能
 */
trait Basic
{

    /**
     * 返回操作对象
     * @return mixed
     */
    abstract public function prototype();

    /**
     * 待实现的安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * @param mixed $value 要安全化的值
     * @return string
     */
    abstract protected function parseValue($value);

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int|null SELECT语句返回数组，INSERT/REPLACE返回自增ID，其余返回受影响行数。
     */
    abstract public function query($sql, array $params = [], callable $callback = null);

    /**
     * 开始事务
     */
    abstract public function startTrans();

    /**
     * 执行事务
     */
    abstract public function commit();

    /**
     * 回滚事务
     */
    abstract public function rollback();

    /**
     * 插入记录，正确时返回自增ID，错误返回false
     * @param array $data 数据
     * @return int 返回自增ID
     */
    public function insert(array $data)
    {
        $this->buildSQL("INSERT", $data);
        $id = $this->query($this->_sql, $this->_params);
        return $id;
    }

    /**
     * 遍历当前结果集
     * 由于少了一层循环和转化，fetch方法比select性能上略有提升，但不方便外部调用，特别是MVC等架构
     * @param callable $func 遍历函数
     */
    public function fetch(callable $func)
    {
        $this->buildSQL("SELECT");
        $this->query($this->_sql, $this->_params, $func);
    }

    /**
     * 删除记录
     * @return int 返回受影响记录条数
     */
    public function delete()
    {
        $this->buildSQL("DELETE");
        return $this->query($this->_sql, $this->_params);
    }

    /**
     * 更新记录
     * @param array $data 要设置的数据
     * @return int 返回受影响记录条数
     */
    public function update($data)
    {
        $this->buildSQL('UPDATE', $data);
        return $this->query($this->_sql, $this->_params);
    }
}