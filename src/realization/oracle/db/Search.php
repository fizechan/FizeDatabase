<?php


namespace fize\db\realization\oracle\db;


trait Search
{
    /**
     * 执行查询，获取单条记录
     * @param mixed $fields 要查询的字段组成的数组(推荐)或者字符串
     * @return mixed 错误返回false，如果无记录则返回空数组
     */
    public function find($fields = null)
    {
        if (!is_null($fields)) {
            $this->field($fields);
        }
        $this->top(1);
        $this->buildSQL("SELECT");
        $result = $this->query($this->sql, $this->params);
        if ($result === false) {
            return false;  //错误
        }
        if (is_array($result) && isset($result[0])) {
            return $result[0];
        } else {
            return [];  //空
        }
    }
}