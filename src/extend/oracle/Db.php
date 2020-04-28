<?php


namespace fize\db\extend\oracle;


use fize\db\core\Db as Base;


/**
 * 数据库
 *
 * Oracle的ORM模型
 */
abstract class Db extends Base
{
    use Feature;

    /**
     * @var string LIMIT语句
     */
    protected $limit = "";

    /**
     * 设置LIMIT,支持链式调用
     * @param int $rows   要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    public function limit($rows, $offset = null)
    {
        if (is_null($offset)) {
            $this->limit = (string)$rows;
        } else {
            $this->limit = (string)$offset . "," . (string)$rows;
        }
        return $this;
    }

    /**
     * NATURAL JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalJoin($table)
    {
        return $this->join($table, "NATURAL JOIN");
    }

    /**
     * NATURAL LEFT JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalLeftJoin($table)
    {
        return $this->join($table, "NATURAL LEFT JOIN");
    }

    /**
     * NATURAL LEFT OUTER JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalLeftOuterJoin($table)
    {
        return $this->join($table, "NATURAL LEFT OUTER JOIN");
    }

    /**
     * NATURAL RIGHT JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalRightJoin($table)
    {
        return $this->join($table, "NATURAL RIGHT JOIN");
    }

    /**
     * NATURAL RIGHT OUTER JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalRightOuterJoin($table)
    {
        return $this->join($table, "NATURAL RIGHT OUTER JOIN");
    }

    /**
     * 清空当前条件，以便于下次查询
     */
    protected function clear()
    {
        parent::clear();
        $this->limit = "";
    }

    /**
     * 根据当前条件构建SQL语句
     * @param string $action SQL语句类型
     * @param array  $data   可能需要的数据
     * @param bool   $clear  是否清理当前条件，默认true
     * @return string 最后组装的SQL语句
     */
    public function build($action, array $data = [], $clear = true)
    {
        $sql = parent::build($action, $data, false);
        if (!empty($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
        }
        $this->sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }
}
