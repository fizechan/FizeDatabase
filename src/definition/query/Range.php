<?php

namespace fize\db\definition\query;

/**
 * DB范围条件查询
 */
trait Range
{
    /**
     * 使用“BETWEEN...AND”语句设置条件
     * @param mixed $value1 值1
     * @param mixed $value2 值2
     * @return $this
     */
    public function between($value1, $value2)
    {
        $preg = '/[,=\>\<\'\"\(\)\?\s]/';
        if (preg_match($preg, (string)$value1) || preg_match($preg, (string)$value2)) {
            return $this->exp("BETWEEN ? AND ?", [$value1, $value2]);
        } else {
            if (is_string($value1) || is_string($value2)) {
                return $this->exp("BETWEEN '{$value1}' AND '{$value2}'");
            } else {
                return $this->exp("BETWEEN {$value1} AND {$value2}");
            }
        }
    }

    /**
     * 使用“NOT BETWEEN...AND”语句设置条件
     * @param mixed $value1 值1
     * @param mixed $value2 值2
     * @return $this
     */
    public function notBetween($value1, $value2)
    {
        $preg = '/[,=\>\<\'\"\(\)\?\s]/';
        if (preg_match($preg, (string)$value1) || preg_match($preg, (string)$value2)) {
            return $this->exp("NOT BETWEEN ? AND ?", [$value1, $value2]);
        } else {
            if (is_string($value1) || is_string($value2)) {
                return $this->exp("NOT BETWEEN '{$value1}' AND '{$value2}'");
            } else {
                return $this->exp("NOT BETWEEN {$value1} AND {$value2}");
            }
        }
    }

    /**
     * 使用“EXISTS”子语句设置条件，使用EXISTS语句时不需要指定对象obj，指定时在exists方法中也没有任何作用，但可以作为对象内条件合并使用
     * @param string $expression EXISTS语句部分、注意是不含EXISTS
     * @param mixed $bind 参数绑定数组
     * @return $this
     */
    public function exists($expression, $bind = null)
    {
        if ($bind === false) {  // exists语句的False值等同于None，做兼容性处理
            $bind = null;
        }
        $obj = $this->obj;  //暂存当前操作对象
        $this->obj = null;
        $query = $this->exp("EXISTS ({$expression})", $bind);
        $this->obj = $obj;  // 还原obj
        return $query;
    }

    /**
     * 使用“NOT EXISTS”子语句设置条件，使用EXISTS语句时不需要指定对象obj，指定时在exists方法中也没有任何作用，但可以作为对象内条件合并使用
     * @param string $expression EXISTS语句部分、注意是不含EXISTS
     * @param mixed $bind 参数绑定数组
     * @return $this
     */
    public function notExists($expression, $bind = null)
    {
        if ($bind === false) {  // exists语句的False值等同于None，做兼容性处理
            $bind = null;
        }
        $obj = $this->obj;  //暂存当前操作对象
        $this->obj = null;
        $query = $this->exp("NOT EXISTS ({$expression})", $bind);
        $this->obj = $obj;  // 还原obj
        return $query;
    }

    /**
     * 使用“IN”语句设置条件
     * @param mixed $values 可以传入数组(推荐)，或者IN条件对应字符串(左右括号可选)
     * @return $this
     */
    public function isIn($values)
    {
        if (is_array($values)) {
            $shuld_holder = false;  //是否需要使用占位符
            foreach ($values as $value) {
                if (preg_match('/[,=\>\<\'\"\(\)\?\s]/', (string)$value)) {
                    $shuld_holder = true;
                    break;
                }
            }
            if ($shuld_holder) {
                $holders = array_fill(0, count($values), "?");
                return $this->exp("IN (" . implode(",", $holders) . ")", $values);
            } else {
                $mider = "";
                foreach ($values as $value) {
                    if (is_string($value)) {
                        $value = "'{$value}'";  // 字符串加上前后引号
                    }
                    if ($mider != "") {
                        $mider .= ", ";
                    }
                    $mider .= $value;
                }
                return $this->exp("IN ({$mider})");
            }
        } else {
            if (substr($values, 0, 1) == "(" && substr($values, -1, 1) == ")") {  // 兼容性判断values是否已自带左右括号
                return $this->exp("IN {$values}");
            } else {
                return $this->exp("IN ({$values})");
            }
        }
    }

    /**
     * 使用“NOT IN”语句设置条件
     * @param mixed $values 可以传入数组(推荐)，或者IN条件对应字符串(左右括号可选)
     * @return $this
     */
    public function notIn($values)
    {
        if (is_array($values)) {
            $shuld_holder = false;  //是否需要使用占位符
            foreach ($values as $value) {
                if (preg_match('/[,=\>\<\'\"\(\)\?\s]/', (string)$value)) {
                    $shuld_holder = true;
                    break;
                }
            }
            if ($shuld_holder) {
                $holders = array_fill(0, count($values), "?");
                return $this->exp("NOT IN (" . implode(",", $holders) . ")", $values);
            } else {
                $mider = "";
                foreach ($values as $value) {
                    if (is_string($value)) {
                        $value = "'{$value}'";  // 字符串加上前后引号
                    }
                    if ($mider != "") {
                        $mider .= ", ";
                    }
                    $mider .= $value;
                }
                return $this->exp("NOT IN ({$mider})");
            }
        } else {
            if (substr($values, 0, 1) == "(" && substr($values, -1, 1) == ")") {  // 兼容性判断values是否已自带左右括号
                return $this->exp("NOT IN {$values}");
            } else {
                return $this->exp("NOT IN ({$values})");
            }
        }
    }

    /**
     * 使用“LIKE”语句设置条件
     * @param string $value LIKE字符串
     * @return $this
     */
    public function like($value)
    {
        return $this->condition("LIKE", $value);
    }

    /**
     * 使用“NOT LIKE”语句设置条件
     * @param string $value LIKE字符串
     * @return $this
     */
    public function notLike($value)
    {
        return $this->condition("NOT LIKE", $value);
    }

    /**
     * 使用“IS NULL”语句设置条件
     * @return $this
     */
    public function isNull()
    {
        return $this->exp("IS NULL");
    }

    /**
     * 使用“IS NOT NULL”语句设置条件
     * @return $this
     */
    public function notNull()
    {
        return $this->exp("IS NOT NULL");
    }
}