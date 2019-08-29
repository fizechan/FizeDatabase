<?php


namespace fize\db\realization\mssql\db;

use fize\db\realization\mssql\Query;


/**
 * MSSQL数据库语句构造trait
 */
trait Build
{

    /**
     * 设置WHERE语句,支持链式调用
     * @param mixed $statements “Query对象”或者“查询数组”或者“WHERE子语句”，其中“WHERE子语句”支持原生的PDO问号预处理占位符;
     * @param array $parse 如果$statements是SQL预处理语句，则可以传递本参数用于预处理替换参数数组
     * @return $this
     */
    public function where($statements, array $parse = [])
    {
        if (is_array($statements)) {  // 通常情况下，我们使用简洁方式来更简便地定义条件，对于复杂条件无法满足的，可以使用查询器或者直接使用预处理语句
            $query = new Query();
            $query->analyze($statements);
            $this->where = $query->sql();
            $this->whereParams = $query->params();
        } else {
            parent::where($statements, $parse);
        }
        return $this;
    }

    /**
     * HAVING语句，支持链式调用
     * @param mixed $statements “Query对象”或者“查询数组”或者“WHERE子语句”，其中“WHERE子语句”支持原生的PDO问号预处理占位符;
     * @param array $parse 如果$statements是SQL预处理语句，则可以传递本参数用于预处理替换参数数组
     * @return $this
     */
    public function having($statements, array $parse = [])
    {
        if (is_array($statements)) {  // 通常情况下，我们使用简洁方式来更简便地定义条件，对于复杂条件无法满足的，可以使用查询器或者直接使用预处理语句
            $query = new Query();
            $query->analyze($statements);
            $this->having = $query->sql();
            $this->havingParams = $query->params();
        } else {
            parent::having($statements, $parse);
        }
        return $this;
    }

    /**
     * 根据当前条件构建SQL语句
     * @param string $action SQL语句类型
     * @param array $data 可能需要的数据
     * @param bool $clear 是否清理当前条件，默认true
     * @return string 最后组装的SQL语句
     */
    protected function buildSQL($action, array $data = [], $clear = true)
    {
        if (!is_null($this->offset) && !$this->new_feature) {  // 进行偏移量移动的旧版SQL组装
            if (empty($this->field)) {
                $this->field = "*";
            }
            if (empty($this->order)) {
                $order = "ORDER BY RAND()";
            } else {
                $order = "ORDER BY " . $this->order;
            }
            $this->order = '';  //消除子语句使用ORDER BY语句的错误
            $this->field .= ", ROW_NUMBER() OVER ({$order}) AS _RN_";
        }

        if ($action == 'TRUNCATE') {
            $sql = "TRUNCATE TABLE {$this->_table_($this->tablePrefix . $this->tableName)}";
            $this->sql = $sql;
            return $sql; //TRUNCATE语句已完整
        } else {
            $sql = parent::buildSQL($action, $data, false);
        }

        if (!empty($this->size) && empty($this->offset)) {
            $sql = substr_replace($sql, " TOP {$this->size}", 6, 0);
        }

        if (!is_null($this->_offset)) {  // 进行偏移量移动
            if ($this->new_feature) {
                //自MSSQL2012开始支持“OFFSET 1 ROWS FETCH NEXT 3 ROWS ONLY”语句
                if (empty($this->order)) {  //OFFSET 需要与ORDER BY语句一起使用，如果没有指定ORDER BY 则使用RAND()
                    $sql .= " ORDER BY RAND() OFFSET {$this->offset} ROWS FETCH NEXT {$this->size} ROWS ONLY";
                } else {
                    $sql .= " OFFSET {$this->offset} ROWS FETCH NEXT {$this->size} ROWS ONLY";
                }
            } else {
                $sql = "SELECT TOP {$this->size} * FROM ({$sql}) AS _TT_ WHERE _TT_._RN_ > {$this->offset}";
            }
        }

        $this->sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }
}