<?php

namespace fize\db\extend\pgsql;

use fize\db\core\Query as Base;

/**
 * 查询器
 *
 * PostgreSQL查询器，占位符统一为问号
 */
class Query extends Base
{
    use Feature;

    /**
     * 使用“REGEXP”语句设置条件
     * @param string $value REGEXP正则字符串
     * @return $this
     */
    public function regExp($value)
    {
        return $this->condition("REGEXP", $value);
    }

    /**
     * 使用“NOT REGEXP”语句设置条件
     * @param string $value NOT REGEXP正则字符串
     * @return $this
     */
    public function notRegExp($value)
    {
        return $this->condition("NOT REGEXP", $value);
    }

    /**
     * 使用“RLIKE”语句设置条件
     * @param string $value RLIKE正则字符串
     * @return $this
     */
    public function rLike($value)
    {
        return $this->condition("RLIKE", $value);
    }

    /**
     * 使用“NOT RLIKE”语句设置条件
     * @param string $value NOT RLIKE正则字符串
     * @return $this
     */
    public function notRLike($value)
    {
        return $this->condition("NOT RLIKE", $value);
    }

    /**
     * 对当前对象解析一个数组条件
     * @param array $value 数组组成的条件
     */
    protected function analyzeArrayQuery(array $value)
    {
        if (is_string($value[0])) {
            switch (strtoupper(trim($value[0]))) {
                case "NOT REGEXP":
                    $this->notRegExp($value[1]);
                    return;
                case "NOT RLIKE":
                    $this->notRLike($value[1]);
                    return;
                case "REGEXP":
                    $this->regExp($value[1]);
                    return;
                case "RLIKE":
                    $this->rLike($value[1]);
                    return;
            }
        }
        parent::analyzeArrayQuery($value);
    }

    /**
     * 以XOR形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param array $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     */
    public static function qXor(...$querys)
    {
        if (count($querys) < 1) {
            return new static();
        }
        $sql = "";
        $bind = [];
        for ($i = 0; $i < count($querys); $i++) {
            if (is_array($querys[$i])) {
                $query = new static();
                $query->analyze($querys[$i]);
            } else {
                $query = $querys[$i];
            }
            if ($i == 0) {
                $sql = "( " . $query->sql() . ")";
                $bind = $query->params();
            } else {
                $sql .= " XOR (" . $query->sql() . ")";
                $bind = array_merge($bind, $query->params());
            }
        }
        return new static(null, $sql, $bind);
    }
}
