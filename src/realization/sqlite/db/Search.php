<?php


namespace fize\db\realization\sqlite\db;


trait Search
{

    /**
     * 执行查询，获取单条记录
     * @param mixed $fields 要查询的字段组成的数组(推荐)或者字符串
     * @return array 如果无记录则返回空数组
     */
    public function findOrNull($fields = null)
    {
        if (!is_null($fields)) {
            $this->field($fields);
        }
        $this->limit(1);
        $this->buildSQL("SELECT");
        $result = $this->query($this->sql, $this->params);
        if (is_array($result) && isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }

}