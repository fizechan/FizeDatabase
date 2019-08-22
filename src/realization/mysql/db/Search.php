<?php


namespace fize\db\realization\mysql\db;


/**
 * Mysql数据库查找功能
 */
trait Search
{

    /**
     * 执行查询，获取单条记录
     * @return array 如果无记录则返回null
     */
    public function findOrNull()
    {
        $this->limit(1);
        $this->buildSQL("SELECT");
        $result = $this->query($this->_sql, $this->_params);
        if(is_array($result) && isset($result[0])){
            return $result[0];
        }else{
            return null;
        }
    }
}