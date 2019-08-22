<?php

namespace fize\db\definition\query;

/**
 * DB解析数组查询
 */
trait Analyze
{
    /**
     * 对当前对象解析一个数组条件
     * @param array $value 数组组成的条件
     */
    protected function analyzeArrayQuery(array $value)
    {
        $count = count($value);
        if ($count == 3) {  // 通常情况下第三位为组合逻辑判断位
            $this->combineLogic($value[2]);
        } else {  // 简洁方式下每下一次都会自动将组合逻辑重置为AND
            $this->combineLogic("AND");
        }
        if (is_null($value[0])) {  // 如果该表达式为null则认为就是isNull判断
            if (isset($value[1]) && !isset($value[2])) {  // null支持第二位进行组合逻辑判断
                $this->combineLogic($value[1]);
            }
            $this->isNull();
        } else {
            switch (strtoupper(trim($value[0]))) {
                case "BETWEEN":
                    if ($count == 2) {  // 该种情况下，参数2必须为数组，且硬性规定必须参数数组长度为2，否则废弃
                        if (is_array($value[1]) && count($value[1]) == 2) {
                            $this->between($value[1][0], $value[1][1]);
                        }
                    } elseif ($count == 3) {  // 3个参数的情况分两种
                        if (is_array($value[1]) && count($value[1]) == 2) {  //参数2是最终所需的参数
                            $this->between($value[1][0], $value[1][1]);
                        } else {  //如果第二个参数不是数组的话，当第3个参数是BETWEEN的第二个值
                            $this->combineLogic('AND');
                            $this->between($value[1], $value[2]);
                        }
                    } else {  // 4个参数的情况下，2、3参数是数值，4参数是组合逻辑
                        $this->combineLogic($value[3]);
                        $this->between($value[1], $value[2]);
                    }
                    break;
                case "CONDITION":
                    if ($count == 2) {  // CONDITION下，参数个数必须大于等于3个，否则废弃。
                        break;
                    } elseif ($count == 3) {  // 该种情况下，参数2为判断符，参数3为值
                        $this->condition($value[1], $value[2]);
                    } elseif ($count == 4) {  // 4个参数的情况分两种
                        if (is_bool($value[3])) {  //第4个参数为bool值时则认为该参数是组合逻辑(不建议使用该方式)
                            $this->combineLogic($value[3]);
                            $this->condition($value[1], $value[2]);
                        } else {  //默认认为参数2为判断符，参数3为值，参数4为绑定值或者数组
                            $this->condition($value[1], $value[2], $value[3]);
                        }
                    } else {  // 5个参数的情况下，参数2为判断符，参数3为值，参数4为绑定值或者数组，参数5是组合逻辑
                        $this->combineLogic($value[4]);
                        $this->condition($value[1], $value[2], $value[3]);
                    }
                    break;
                case "EGT":
                case ">=":
                    $this->egt($value[1]);
                    break;
                case "ELT":
                case "<=":
                    $this->elt($value[1]);
                    break;
                case "EQ":
                case "=":
                    $this->eq($value[1]);
                    break;
                case "GT":
                case ">":
                    $this->gt($value[1]);
                    break;
                case "IN":
                    $this->isIn($value[1]);
                    break;
                case "LIKE":
                    $this->like($value[1]);
                    break;
                case "LT":
                case "<":
                    $this->lt($value[1]);
                    break;
                case "NEQ":
                case "<>":
                case "!=":
                    $this->neq($value[1]);
                    break;
                case "NULL":
                case "IS NULL":
                    if (isset($value[1]) && !isset($value[2])) {  // null支持第二位进行组合逻辑判断
                        $this->combineLogic($value[1]);
                    }
                    $this->isNull();
                    break;
                case "NOT NULL":
                case "IS NOT NULL":
                    if (isset($value[1]) && !isset($value[2])) {  // is not null支持第二位进行组合逻辑判断
                        $this->combineLogic($value[1]);
                    }
                    $this->notNull();
                    break;
                case "NOT BETWEEN":
                    if ($count == 2) {  // 该种情况下，参数2必须为数组，且硬性规定必须参数数组长度为2，否则废弃
                        if (is_array($value[1]) && count($value[1]) == 2) {
                            $this->notBetween($value[1][0], $value[1][1]);
                        }
                    } elseif ($count == 3) {  // 3个参数的情况分两种
                        if (is_array($value[1]) && count($value[1]) == 2) {  //参数2是最终所需的参数
                            $this->notBetween($value[1][0], $value[1][1]);
                        } else {
                            $this->combineLogic('AND');
                            $this->notBetween($value[1], $value[2]);
                        }
                    } else {  // 4个参数的情况下，2、3参数是数值，4参数是组合逻辑
                        $this->combineLogic($value[3]);
                        $this->notBetween($value[1], $value[2]);
                    }
                    break;
                case "NOT IN":
                    $this->notIn($value[1]);
                    break;
                case "NOT LIKE":
                    $this->notLike($value[1]);
                    break;
                default :  // 默认认为是参数1为完整表达式，参数2为可能需要的绑定参数
                    $bind = isset($value[1]) ? $value[1] : null;
                    $this->exp($value[0], $bind);
            }
        }
    }

    /**
     * 解析一个条件数组，返回Query
     * @param array $maps 一定格式的条件数组
     * @return $this
     */
    public function analyze(array $maps)
    {
        foreach ($maps as $key => $value) {
            if (is_string($key)) {  // $key为字段名
                $this->obj($key);
                if (is_array($value)) {
                    $this->analyzeArrayQuery($value);
                } else {  //非数组情况下，如果value为null则使用isNull，否则认为“=”
                    $this->combineLogic("AND");  //将默认组合逻辑改为AND
                    if (is_null($value)) {
                        $this->isNull();
                    } else {
                        $this->eq($value);
                    }
                }
            } else {  // 没有显式指定$key则认为使用不指定字段名的子语句
                $this->combineLogic("AND");  //将默认组合逻辑改为AND
                $this->_obj = null;
                if (is_array($value)) {
                    $count = count($value);
                    switch (strtoupper(trim($value[0]))) {
                        case "EXISTS":
                            if ($count > 1) {  // EXISTS条件必须再继续带参数，否则废弃
                                $logic = isset($value[3]) ? $value[3] : "AND";
                                $bind = isset($value[2]) ? $value[2] : null;
                                $this->combineLogic($logic);
                                $this->exists($value[1], $bind);
                            }
                            break;
                        case "NOT EXISTS":
                            if ($count > 1) {  // NOT EXISTS条件必须再继续带参数，否则废弃
                                $logic = isset($value[3]) ? $value[3] : "AND";
                                $bind = isset($value[2]) ? $value[2] : null;
                                $this->combineLogic($logic);
                                $this->notExists($value[1], $bind);
                            }
                            break;
                        default :  //  默认情况下，认为是表达式语句
                            $logic = isset($value[3]) ? $value[3] : "AND";
                            $bind = isset($value[1]) ? $value[1] : null;
                            $this->combineLogic($logic);
                            $this->exp($value[0], $bind);
                    }
                } else {  //$value作为字符时，认为是SQL表达式
                    $this->exp($value);
                }
            }
        }
        return $this;
    }
}