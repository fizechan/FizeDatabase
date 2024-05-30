<?php

namespace Fize\Database\Core;

class Where
{

    /**
     * 设置本对象当前每个条件的组合逻辑
     *
     * 参数 `$logic` :
     *   不区分大小写，未调用该方法是默认组合逻辑为“AND”
     * @param string $logic 组合逻辑
     * @return $this
     */
    public function logic(string $logic): Query
    {
        $this->logic = $logic;
        return $this;
    }

    /**
     * 以指定形式组合Query对象,或者指可以使用analyze()的数组
     *
     * @param string      $logic 组合逻辑
     * @param Query|array $query 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     * @todo 待转移到外部
     */
    public function qMerge(string $logic, $query): Query
    {
        if (is_array($query)) {
            $maps = $query;
            $query = new static();
            $query->analyze($maps);
        }
        $this->sql = "($this->sql) {$logic} (" . $query->sql() . ")";
        $this->params = array_merge($this->params, $query->params());
        return $this;
    }

    /**
     * 以AND形式组合Query对象,或者指可以使用analyze()的数组
     *
     * @param Query|array $query 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     * @todo 待转移到外部
     */
    public function qAnd($query): Query
    {
        return $this->qMerge('AND', $query);
    }

    /**
     * 以OR形式组合Query对象,或者指可以使用analyze()的数组
     *
     * @param Query|array $query 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     * @todo 待转移到外部
     */
    public function qOr($query): Query
    {
        return $this->qMerge('OR', $query);
    }

}