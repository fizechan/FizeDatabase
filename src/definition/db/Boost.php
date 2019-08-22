<?php

namespace fize\db\definition\db;

/**
 * 数据库模型抽象类增强功能
 */
trait Boost
{

    /**
     * 完整分页，执行该方法可以获取到分页记录、完整记录数、总页数，可用于分页输出
     * @todo 寻找更好的方案
     * @param int $page 页码
     * @param int $size 每页记录数量，默认每页10个
     * @return array [count、pages、rows]
     */
    public function paginate($page, $size = 10)
    {
        $sql_temp = $this->buildSQL("SELECT", [], false);
        //var_dump($sql_temp);
        $sql_for_count = substr_replace($sql_temp, "COUNT(*)", 7, strlen($this->_field));
        if(!empty($this->_order)){  //消除ORDER BY 语句对COUNT语句的影响问题
            $sql_for_count = str_replace(" ORDER BY {$this->_order}", "", $sql_for_count);
        }
        //var_dump($sql_for_count);
        $rows_for_count = $this->query($sql_for_count, $this->_params);
        $count = (int)array_values($rows_for_count[0])[0];  //第一列第一个值
        $this->page($page, $size);
        $this->buildSQL("SELECT");
        //var_dump($this->_sql);
        $result = $this->query($this->_sql, $this->_params);
        return [
            $count,  //记录个数
            (int)ceil($count / $size),  //总页数
            $result  //当前返回的分页记录数组
        ];
    }

    /**
     * 批量插入记录
     * @param array $datas 要插入的记录组成的数组
     */
    public function insertAll($datas)
    {
        foreach ($datas as $data) {
            $this->insert($data);
        }
    }
}