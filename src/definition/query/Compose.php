<?php

namespace fize\db\definition\query;


/**
 * DB组合条件查询
 */
trait Compose
{
    /**
     * 以AND形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param array $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     */
    public static function qAnd(...$querys)
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
                $sql .= " AND (" . $query->sql() . ")";
                $bind = array_merge($bind, $query->params());
            }
        }
        return new static(null, $sql, $bind);
    }

    /**
     * 以OR形式组合多个Query对象,或者指可以使用analyze()的数组
     * @param array $querys 可以是Query对象或者指可以使用analyze()的数组
     * @return $this
     */
    public static function qOr(...$querys)
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
                $sql .= " OR (" . $query->sql() . ")";
                $bind = array_merge($bind, $query->params());
            }
        }
        return new static(null, $sql, $bind);
    }
}