<?php

namespace fize\db\definition\db;


use fize\db\exception\DbException;

/**
 * 数据库语句构造trait
 */
trait Build
{

    /**
     * 最后组装的SQL语句
     * @var string
     */
    protected $_sql = "";

    /**
     * 语句中所有按顺序绑定的参数
     * @var array
     */
    protected $_params = [];

    /**
     * 获取最后运行的SQL
     * 仅供日志使用的SQL语句，由于本身存在SQL危险请不要真正用于执行
     * @param bool $real 是否返回最终SQL语句而非预处理语句
     * @return string
     */
    public function getLastSql($real = false)
    {
        if ($real) {
            $temp = explode('?', $this->_sql);
            $last_sql = "";
            for ($i = 0; $i < count($temp) - 1; $i++) {
                $last_sql .= $temp[$i] . $this->parseValue($this->_params[$i]);
            }
            $last_sql .= $temp[count($temp) - 1];
            return $last_sql;
        } else {
            return $this->_sql;
        }
    }

    /**
     * 解析插入数值的SQL部分语句，用于数值原样写入
     * @param array $datas 要写入的数值数组
     * @param array $params 可能要操作的参数数组
     * @return string
     */
    protected function parseInsertDatas(array $datas, array &$params = [])
    {
        $fields = []; //字段名
        $holdes = []; //占位符
        foreach ($datas as $key => $val) {
            $fields[] = $this->_field_($key);
            if (is_array($val)) {  //传递数组则认为是原值写入(添加时应该没有这样的使用)
                $holdes[] = $val[0];
            } else {
                $holdes[] = "?";
                $params[] = $val;
            }
        }
        return '(' . implode(',', $fields) . ') VALUES (' . implode(',', $holdes) . ')';
    }

    /**
     * 解析更新数值的SQL部分语句，用于数值原样更新
     * @param array $datas 要更新的数值数组
     * @param array $params 可能要操作的参数数组
     * @return string
     */
    protected function parseUpdateDatas(array $datas, array &$params = [])
    {
        $out = "";
        foreach ($datas as $key => $val) {
            if (!empty($out)) {
                $out .= " , ";
            }
            if (is_array($val)) {  //传递数组则认为是原值写入
                $out .= "{$this->_field_($key)} = {$val[0]}";
            } else {
                $out .= "{$this->_field_($key)} = ?";
                $params[] = $val;
            }
        }
        return $out;
    }

    /**
     * 清空当前条件，以便于下次查询
     * 子类可根据需要进行重写
     */
    protected function clear()
    {
        //以下注释请不要删除，用于提示不需要重置的条件
        //$this->_tablePrefix = "";
        //$this->_tableName = "";
        //$this->_sql = "";
        //$this->_params = [];

        //清空一次性条件
        $this->_alias = "";
        $this->_join = "";
        $this->_where = "";
        $this->_group = "";
        $this->_having = "";
        $this->_field = "";
        $this->_order = "";
        $this->_union = "";
        $this->_whereParams = [];
        $this->_havingParams = [];
    }

    /**
     * 根据当前条件构建SQL语句
     * 子类可根据需要进行重写
     * @param string $action SQL语句类型
     * @param array $data 可能需要的数据
     * @param bool $clear 是否清理当前条件，默认true
     * @return string 最后组装的SQL语句
     * @throws DbException
     */
    protected function buildSQL($action, array $data = [], $clear = true)
    {
        switch ($action) {
            case "DELETE" : //删除
                $sql = "DELETE FROM {$this->_table_($this->_tablePrefix. $this->_tableName)}";
                $this->_params = $this->_whereParams + $this->_havingParams;
                break;
            case "INSERT" : //添加
                $params = [];
                $sql = "INSERT INTO {$this->_table_($this->_tablePrefix. $this->_tableName)}{$this->parseInsertDatas($data, $params)}";
                $this->_params = $params;
                break;
            case "SELECT" : //查询
                if (empty($this->_field)) {
                    $this->_field = "*";
                }
                $sql = "SELECT {$this->_field} FROM {$this->_table_($this->_tablePrefix. $this->_tableName)}";
                $this->_params = $this->_whereParams + $this->_havingParams;
                break;
            case "UPDATE" : //更新
                $data_params = [];
                $sql = "UPDATE {$this->_table_($this->_tablePrefix. $this->_tableName)} SET {$this->parseUpdateDatas($data, $data_params)}";
                $this->_params = array_merge($data_params, $this->_whereParams, $this->_havingParams);
                break;
            default :
                //仅需要支持DELETE、INSERT、REPLACE、SELECT、UPDATE，防止其他语句进入
                throw new DbException("Illegal SQL statement: {$action}");
        }
        if (in_array($action, ['DELETE', 'SELECT', 'UPDATE'])) {
            if (!empty($this->_alias)) {
                $sql .= " AS {$this->_field_($this->_alias)}";
            }
            if (!empty($this->_join)) {
                $sql .= " {$this->_join}";
            }
            if (!empty($this->_where)) {
                $sql .= " WHERE {$this->_where}";
            }
            if (!empty($this->_group)) {
                $sql .= " GROUP BY {$this->_group}";
            }
            if (!empty($this->_having)) {
                $sql .= " HAVING {$this->_having}";
            }
            $sql .= $this->_union;
            if (!empty($this->_order)) {
                $sql .= " ORDER BY {$this->_order}";
            }
        }
        $this->_sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }
}