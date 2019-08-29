<?php


namespace fize\db\definition;


/**
 * 条件查询器，占位符统一为问号
 * @package fize\db\definition
 */
class Query
{
    use Feature;

    /**
     * @var string 操作对象
     */
    protected $object = null;

    /**
     * @var array 绑定参数
     */
    protected $params = [];

    /**
     * @var string 合并逻辑
     */
    protected $logic = "AND";

    /**
     * @var string 生成的SQL语句片段
     */
    protected $sql = "";

    /**
     * 构造
     * @param string $object 要进行判断的对象，一般为字段名
     */
    public function __construct($object = null)
    {
        $this->object($object);
    }

    /**
     * 设置本对象当前每个条件的组合逻辑
     * @param mixed $logic 组合逻辑，不区分大小写，未调用该方法是默认组合逻辑为“AND”,特殊值true表示AND，false表示OR
     * @return $this
     */
    public function logic($logic)
    {
        if ($logic === true) {
            $logic = "AND";
        }
        if ($logic === false) {
            $logic = "OR";
        }
        $this->logic = $logic;
        return $this;
    }

    /**
     * 设定当前操作对象
     * @param string $object 操作对象，通常为字段名
     * @return $this
     */
    public function object($object)
    {
        if (is_string($object)) {
            $this->object = $this->formatField($object);
        } else {
            $this->object = $object;
        }
        return $this;
    }

    /**
     * 设定当前操作字段
     * 实际上是object方法的别名
     * @param string $field_name 字段名
     * @return $this
     */
    public function field($field_name)
    {
        return $this->object($field_name);
    }

    /**
     * 返回查询语句SQL预处理语句块
     * @return string
     */
    public function sql()
    {
        return $this->sql;
    }

    /**
     * 获取完整的参数绑定数组
     * @return array
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * 使用原始表达式语句设置条件
     * @param string $expression 表达式语句
     * @param mixed $params 要绑定的数组，如果是单个绑定可以直接传入值，不需要绑定请不传递或者传递null
     * @return $this
     */
    public function exp($expression, $params = null)
    {
        if ($this->sql == "") {
            if ($this->object == null) {
                $this->sql = $expression;
            } else {
                $this->sql = $this->object . " " . $expression;
            }
        } else {
            if ($this->object == null) {
                $this->sql .= " " . $this->logic . " " . $expression;
            } else {
                $this->sql .= " " . $this->logic . " " . $this->object . " " . $expression;
            }
        }
        if (!is_null($params)) {
            if (is_array($params)) {
                $this->params = array_merge($this->params, $params);
            } else {
                $this->params[] = $params;
            }
        }
        return $this;
    }

    /**
     * 使用条件语句设置条件
     * @param string $judge 判断符
     * @param mixed $value 判断量，该值必须为标量
     * @param mixed $params 参数绑定数组，特殊值false表示不绑定参数，null表示自动判断是否绑定
     * @return $this
     */
    public function condition($judge, $value, $params = null)
    {
        if ($params === false) {  // false表示不需要绑定参数
            if (is_string($value)) {
                return $this->exp($judge . " '" . addslashes($value) . "'");
            } else {
                return $this->exp($judge . " " . (string)$value);
            }
        } else {
            if (is_null($params) && is_string($value)) {  // null表示自动判断是否绑定参数，如果此时参数为字符串形式则必须进行绑定
                if (preg_match('/[,=\>\<\'\"\(\)\?\s]/', $value)) {
                    return $this->exp($judge . " ?", [$value]);
                } else {
                    return $this->exp($judge . " '" . addslashes($value) . "'");
                }
            } else {
                return $this->exp($judge . " " . (string)$value, $params);  // 对于非字符串格式的，可以不进行绑定，直接写入SQL
            }
        }
    }

    /**
     * 使用条件“大于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function gt($value)
    {
        return $this->condition(">", $value);
    }

    /**
     * 使用条件“大于等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function egt($value)
    {
        return $this->condition(">=", $value);
    }

    /**
     * 使用条件“小于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function lt($value)
    {
        return $this->condition("<", $value);
    }

    /**
     * 使用条件“小于等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function elt($value)
    {
        return $this->condition("<=", $value);
    }

    /**
     * 使用条件“等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function eq($value)
    {
        return $this->condition("=", $value);
    }

    /**
     * 使用条件“不等于”设置条件
     * @param mixed $value 判断值
     * @return $this
     */
    public function neq($value)
    {
        return $this->condition("<>", $value);
    }

    /**
     * 使用“BETWEEN...AND”语句设置条件
     * @param mixed $value1 值1
     * @param mixed $value2 值2
     * @param string $premodifier 前置修饰
     * @return $this
     */
    public function between($value1, $value2, $premodifier = '')
    {
        $preg = '/[,=\>\<\'\"\(\)\?\s]/';
        if (preg_match($preg, (string)$value1) || preg_match($preg, (string)$value2)) {
            return $this->exp(trim("{$premodifier} BETWEEN ? AND ?"), [$value1, $value2]);
        } else {
            if (is_string($value1) || is_string($value2)) {
                return $this->exp(trim("{$premodifier} BETWEEN '{$value1}' AND '{$value2}'"));
            } else {
                return $this->exp(trim("{$premodifier} BETWEEN {$value1} AND {$value2}"));
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
        return $this->between($value1, $value2, 'NOT');
    }

    /**
     * 使用“EXISTS”子语句设置条件，使用EXISTS语句时不需要指定对象object，指定时在exists方法中也没有任何作用，但可以作为对象内条件合并使用
     * @param string $expression EXISTS语句部分、注意是不含EXISTS
     * @param mixed $params 参数绑定数组
     * @param string $premodifier 前置修饰
     * @return $this
     */
    public function exists($expression, $params = null, $premodifier = '')
    {
        if ($params === false) {  // exists语句的false值等同于null，做兼容性处理
            $params = null;
        }
        $object = $this->object;  //暂存当前操作对象
        $this->object = null;  //EXISTS语句不需要object
        $query = $this->exp(trim("{$premodifier} EXISTS ({$expression})"), $params);
        $this->object = $object;  // 还原当前操作对象
        return $query;
    }

    /**
     * 使用“NOT EXISTS”子语句设置条件，使用EXISTS语句时不需要指定对象obj，指定时在exists方法中也没有任何作用，但可以作为对象内条件合并使用
     * @param string $expression EXISTS语句部分、注意是不含EXISTS
     * @param mixed $params 参数绑定数组
     * @return $this
     */
    public function notExists($expression, $params = null)
    {
        return $this->exists($expression, $params, 'NOT');
    }

    /**
     * 使用“IN”语句设置条件
     * @param mixed $values 可以传入数组(推荐)，或者IN条件对应字符串(左右括号可选)
     * @param string $premodifier 前置修饰
     * @return $this
     */
    public function isIn($values, $premodifier = '')
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
                return $this->exp(trim("{$premodifier} IN (" . implode(",", $holders) . ")"), $values);
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
                return $this->exp(trim("{$premodifier} IN ({$mider})"));
            }
        } else {
            if (substr($values, 0, 1) == "(" && substr($values, -1, 1) == ")") {  // 兼容性判断values是否已自带左右括号
                return $this->exp(trim("{$premodifier} IN {$values}"));
            } else {
                return $this->exp(trim("{$premodifier} IN ({$values})"));
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
        return $this->isIn($values, 'NOT');
    }

    /**
     * 使用“LIKE”语句设置条件
     * @param string $value LIKE字符串
     * @param string $premodifier 前置修饰
     * @return $this
     */
    public function like($value, $premodifier = '')
    {
        return $this->condition(trim("{$premodifier} LIKE"), $value);
    }

    /**
     * 使用“NOT LIKE”语句设置条件
     * @param string $value LIKE字符串
     * @return $this
     */
    public function notLike($value)
    {
        return $this->like($value, 'NOT');
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

    /**
     * 对当前对象解析一个数组条件
     * @param array $value 数组组成的条件
     */
    protected function analyzeArrayParams(array $value)
    {
        $count = count($value);
        if ($count == 3) {  // 通常情况下第三位为组合逻辑判断位
            $this->logic($value[2]);
        } else {  // 简洁方式下每下一次都会自动将组合逻辑重置为AND
            $this->logic("AND");
        }
        if (is_null($value[0])) {  // 如果该表达式为null则认为就是isNull判断
            if (isset($value[1]) && !isset($value[2])) {  // null支持第二位进行组合逻辑判断
                $this->logic($value[1]);
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
                            $this->logic('AND');
                            $this->between($value[1], $value[2]);
                        }
                    } else {  // 4个参数的情况下，2、3参数是数值，4参数是组合逻辑
                        $this->logic($value[3]);
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
                            $this->logic($value[3]);
                            $this->condition($value[1], $value[2]);
                        } else {  //默认认为参数2为判断符，参数3为值，参数4为绑定值或者数组
                            $this->condition($value[1], $value[2], $value[3]);
                        }
                    } else {  // 5个参数的情况下，参数2为判断符，参数3为值，参数4为绑定值或者数组，参数5是组合逻辑
                        $this->logic($value[4]);
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
                        $this->logic($value[1]);
                    }
                    $this->isNull();
                    break;
                case "NOT NULL":
                case "IS NOT NULL":
                    if (isset($value[1]) && !isset($value[2])) {  // is not null支持第二位进行组合逻辑判断
                        $this->logic($value[1]);
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
                            $this->logic('AND');
                            $this->notBetween($value[1], $value[2]);
                        }
                    } else {  // 4个参数的情况下，2、3参数是数值，4参数是组合逻辑
                        $this->logic($value[3]);
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
                    $params = isset($value[1]) ? $value[1] : null;
                    $this->exp($value[0], $params);
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
                $this->object($key);
                if (is_array($value)) {
                    $this->analyzeArrayParams($value);
                } else {  //非数组情况下，如果value为null则使用isNull，否则认为“=”
                    $this->logic("AND");  //将默认组合逻辑改为AND
                    if (is_null($value)) {
                        $this->isNull();
                    } else {
                        $this->eq($value);
                    }
                }
            } else {  // 没有显式指定$key则认为使用不指定字段名的子语句
                $this->logic("AND");  //将默认组合逻辑改为AND
                $this->object = null;
                if (is_array($value)) {
                    $count = count($value);
                    switch (strtoupper(trim($value[0]))) {
                        case "EXISTS":
                            if ($count > 1) {  // EXISTS条件必须再继续带参数，否则废弃
                                $logic = isset($value[3]) ? $value[3] : "AND";
                                $params = isset($value[2]) ? $value[2] : null;
                                $this->logic($logic);
                                $this->exists($value[1], $params);
                            }
                            break;
                        case "NOT EXISTS":
                            if ($count > 1) {  // NOT EXISTS条件必须再继续带参数，否则废弃
                                $logic = isset($value[3]) ? $value[3] : "AND";
                                $params = isset($value[2]) ? $value[2] : null;
                                $this->logic($logic);
                                $this->notExists($value[1], $params);
                            }
                            break;
                        default :  //  默认情况下，认为是表达式语句
                            $logic = isset($value[3]) ? $value[3] : "AND";
                            $params = isset($value[1]) ? $value[1] : null;
                            $this->logic($logic);
                            $this->exp($value[0], $params);
                    }
                } else {  //$value作为字符时，认为是SQL表达式
                    $this->exp($value);
                }
            }
        }
        return $this;
    }

    /**
     * 以指定形式组合Query对象,或者指可以使用analyze()的数组
     * @param string $logic 组合逻辑
     * @param mixed $query 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     */
    protected function qMerge($logic, $query)
    {
        if (is_array($query)) {
            $maps = $query;
            $query = new static();
            $query->analyze($maps);
        }
        $this->sql .= " {$logic} (" . $query->sql() . ")";;
        $this->params = array_merge($this->params, $query->params());
        return $this;
    }

    /**
     * 以AND形式组合Query对象,或者指可以使用analyze()的数组
     * @param mixed $query 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     */
    public function qAnd($query)
    {
        return $this->qMerge('AND', $query);
    }

    /**
     * 以OR形式组合Query对象,或者指可以使用analyze()的数组
     * @param mixed $query 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     */
    public function qOr($query)
    {
        return $this->qMerge('OR', $query);
    }
}