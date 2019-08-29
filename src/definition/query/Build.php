<?php

namespace fize\db\definition\query;

/**
 * 数据库语句构造trait
 */
trait Build
{
    protected $obj = null;

    protected $bind = [];

    protected $combine_logic = "AND";

    protected $sql = "";

    /**
     * 构造
     * @todo 参数$_sql、$_bind如何移出不在本处作用？
     * @param string $obj 要进行判断的对象，一般为字段名
     * @param string $_sql 初始化的SQL查询部分构建语句，不建议外部传递该值，主要用于合并查询。
     * @param array $_bind 初始化的参数绑定，不建议外部传递该值，主要用于合并查询。
     */
    public function __construct($obj = null, $_sql = "", array $_bind = [])
    {
        if (is_string($obj)) {
            $this->obj = $this->_field_($obj);
        } else {
            $this->obj = $obj;
        }
        $this->sql = $_sql;
        $this->bind = $_bind;
    }

    /**
     * 设置本对象当前每个条件的组合逻辑
     * @todo 命名不够好，需要修改
     * @param mixed $logic 组合逻辑，不区分大小写，未调用该方法是默认组合逻辑为“AND”,特殊值true表示AND，false表示OR
     * @return $this
     */
    public function combineLogic($logic)
    {
        if ($logic === true) {
            $logic = "AND";
        }
        if ($logic === false) {
            $logic = "OR";
        }
        $this->combine_logic = strtoupper($logic);
        return $this;
    }

    /**
     * 设定当前操作对象
     * @todo 命名不够好，需要修改
     * @param string $obj 操作对象，通常为字段名
     * @return $this
     */
    public function obj($obj)
    {
        if (is_string($obj)) {
            $this->obj = $this->_field_($obj);
        } else {
            $this->obj = $obj;
        }
        return $this;
    }

    /**
     * 设定当前操作字段
     * 实际上是obj方法的别名
     * @param string $obj 操作对象，通常为字段名
     * @return $this
     */
    public function field($obj)
    {
        return $this->obj($obj);
    }

    /**
     * 对本对象添加一个条件块。
     * 注意，对象内添加条件是不添加左右括号的，如果需要添加请使用对象间条件
     * @todo 命名不够完美
     * @param string $statement SQL语句块，支持预处理问号占位符
     * @param mixed $bind 要绑定的数组，如果是单个绑定可以直接传入值，不需要绑定请不传递或者传递null
     */
    protected function _addPart($statement, $bind = null)
    {
        if ($this->sql == "") {
            if ($this->obj == null) {
                $this->sql = $statement;
            } else {
                $this->sql = $this->obj . " " . $statement;
            }
        } else {
            if ($this->obj == null) {
                $this->sql .= " " . $this->combine_logic . " " . $statement;
            } else {
                $this->sql .= " " . $this->combine_logic . " " . $this->obj . " " . $statement;
            }
        }
        if (!is_null($bind)) {
            if (is_array($bind)) {
                $this->bind = array_merge($this->bind, $bind);
            } else {
                $this->bind[] = $bind;
            }
        }
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
        return $this->bind;
    }
}