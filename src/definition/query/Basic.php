<?php

namespace fize\db\definition\query;

/**
 * DB基本条件查询
 */
trait Basic
{
    /**
     * 使用原始表达式语句设置条件
     * @param string $expression 表达式语句
     * @param mixed $bind 要绑定的数组，如果是单个绑定可以直接传入值，不需要绑定请不传递或者传递null
     * @return $this
     */
    public function exp($expression, $bind = null)
    {
        $this->_addPart($expression, $bind);
        return $this;
    }

    /**
     * 使用条件语句设置条件
     * @param string $judge 判断符
     * @param mixed $value 判断量，该值必须为标量
     * @param mixed $bind 参数绑定数组，特殊值False表示不绑定参数，None表示自动判断是否绑定
     * @return $this
     */
    public function condition($judge, $value, $bind = null)
    {
        if ($bind === false) {  // false表示不需要绑定参数
            if (is_string($value)) {
                return $this->exp($judge . " '" . addslashes($value) . "'");
            } else {
                return $this->exp($judge . " " . (string)$value);
            }
        } else {
            if (is_null($bind) && is_string($value)) {  // null表示自动判断是否绑定参数，如果此时参数为字符串形式则必须进行绑定
                if (preg_match('/[,=\>\<\'\"\(\)\?\s]/', $value)) {
                    return $this->exp($judge . " ?", [$value]);
                } else {
                    return $this->exp($judge . " '" . addslashes($value) . "'");
                }
            } else {
                return $this->exp($judge . " " . (string)$value, $bind);  // 对于非字符串格式的，可以不进行绑定，直接写入SQL
            }
        }
    }
}