<?php

namespace fize\db\definition\db;

/**
 * 数据库模型抽象类更新功能
 */
trait Update
{
    /**
     * 设置数据
     * @param mixed $field 字段名
     * @param mixed $value 字段值,数组为原样语句写入，其余为值写入
     * @return int 返回受影响记录条数
     */
    public function setValue($field, $value)
    {
        $data = [$field => $value];
        return $this->update($data);
    }

    /**
     * 字段值增长
     * @param string $field 字段名
     * @param int $step 增长值，默认为1
     * @return int 返回受影响记录条数
     */
    public function setInc($field, $step = 1)
    {
        $data = [$field => ["{$this->_field_($field)} + {$step}"]];
        return $this->update($data);
    }

    /**
     * 字段值减少
     * @param string $field 字段名
     * @param int $step 增长值，默认为1
     * @return int 返回受影响记录条数
     */
    public function setDec($field, $step = 1)
    {
        $data = [$field => ["{$this->_field_($field)} - {$step}"]];
        return $this->update($data);
    }
}