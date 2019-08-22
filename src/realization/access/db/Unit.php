<?php


namespace fize\db\realization\access\db;

use fize\db\realization\access\Query;


trait Unit
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
            $this->_where = $query->sql();
            $this->_whereParams = $query->params();
        } else {
            parent::where($statements, $parse);
        }
        return $this;
    }


}