<?php

namespace Fize\Database\Extend\Oracle;

use Fize\Database\Core\Db as CoreDb;


/**
 * 数据库
 *
 * Oracle的ORM模型
 */
abstract class Db extends CoreDb
{
    use Feature;

    /**
     * @var string LIMIT语句
     */
    protected $limit = "";

    /**
     * 设置LIMIT,支持链式调用
     * @param int      $rows   要返回的记录数
     * @param int|null $offset 要设置的偏移量
     * @return $this
     */
    public function limit(int $rows, int $offset = null)
    {
        if (is_null($offset)) {
            $this->limit = (string)$rows;
        } else {
            $this->limit = $offset . "," . $rows;
        }
        return $this;
    }

    /**
     * NATURAL JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalJoin(string $table): Db
    {
        return $this->join($table, "NATURAL JOIN");
    }

    /**
     * NATURAL LEFT JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalLeftJoin(string $table): Db
    {
        return $this->join($table, "NATURAL LEFT JOIN");
    }

    /**
     * NATURAL LEFT OUTER JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalLeftOuterJoin(string $table): Db
    {
        return $this->join($table, "NATURAL LEFT OUTER JOIN");
    }

    /**
     * NATURAL RIGHT JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalRightJoin(string $table): Db
    {
        return $this->join($table, "NATURAL RIGHT JOIN");
    }

    /**
     * NATURAL RIGHT OUTER JOIN条件,支持链式调用
     * @param string $table 表名
     * @return $this
     */
    public function naturalRightOuterJoin(string $table): Db
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
    protected function build(string $action, array $data = [], bool $clear = true): string
    {
        $sql = parent::build($action, $data, false);
        if (!empty($this->limit)) {
            $sql .= " LIMIT $this->limit";
        }
        $this->sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }
}
