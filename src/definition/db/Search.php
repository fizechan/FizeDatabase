<?php

namespace fize\db\definition\db;

use fize\db\exception\DataNotFoundException;

/**
 * 数据库模型抽象类查找功能
 */
trait Search
{

    /**
     * @var array 缓存中的查询记录
     */
    protected static $cache_rows = [];

    /**
     * LIMIT语句已是事实标准
     * @param int $rows 要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    abstract public function limit($rows, $offset = null);

    /**
     * 执行查询，返回结果记录列表
     * @param bool $cache 是否使用搜索缓存，默认true
     * @return array
     */
    public function select($cache = true)
    {
        $this->buildSQL("SELECT");

        if ($cache) {
            $sql = $this->getLastSql(true);
            if (!isset(self::$cache_rows[$sql])) {
                self::$cache_rows[$sql] = $this->query($this->_sql, $this->_params);
            }
            return self::$cache_rows[$sql];
        }

        $result = $this->query($this->_sql, $this->_params);
        return $result;
    }

    /**
     * 执行查询，获取单条记录
     * @param bool $cache 是否使用搜索缓存，默认false
     * @return array 如果无记录则返回null
     */
    public function findOrNull($cache = false)
    {
        $rows = $this->limit(1, 0)->select($cache);
        if(count($rows) == 0) {
            return null;
        }
        return $rows[0];
    }

    /**
     * 执行查询，获取单条记录,如果未找到则抛出错误
     * @param bool $cache 是否使用搜索缓存，默认false
     * @return array
     * @throws DataNotFoundException
     */
    public function find($cache = false)
    {
        $row = $this->findOrNull($cache);
        if (empty($row)) {
            throw new DataNotFoundException('Recordset Not Found', 0, $this->getLastSql(true));
        }
        return $row;
    }

    /**
     * 得到某个字段的值
     * @access public
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @param bool $force 强制转为数字类型
     * @return mixed 如果$force为true时真返回数字类型
     */
    public function value($field, $default = null, $force = false)
    {
        $this->field([$field]);
        $row = $this->findOrNull(false);
        $result = $default;
        if (!empty($row)) {
            $result = array_values($row)[0];  //第一列第一个值
            if ($force) {
                $result = is_numeric($result) ? $result + 0 : $result;
            }
        }
        return $result;
    }

    /**
     * 得到某个列的数组
     * @param string $field 字段名
     * @return array
     */
    public function column($field)
    {
        $values = [];
        $this->fetch(function ($row) use ($field, &$values) {
            $values[] = $row[$field];
        });
        return $values;
    }

    /**
     * 使用模拟的LIMIT语句进行简易分页,支持链式调用
     * @param int $index 页码
     * @param int $prepg 每页记录数量
     * @return $this
     */
    public function page($index, $prepg = 10)
    {
        $rows = $prepg;
        $offset = ($index - 1) * $prepg;
        return $this->limit($rows, $offset);
    }
}