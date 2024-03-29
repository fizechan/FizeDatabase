<?php

namespace Fize\Database\Extend\SQLite;

use Fize\Database\Core\Db as CoreDb;

/**
 * 数据库
 *
 * Sqlite的ORM模型
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
    public function limit(int $rows, int $offset = null): Db
    {
        if (is_null($offset)) {
            $this->limit = (string)$rows;
        } else {
            $this->limit = $offset . "," . $rows;
        }
        return $this;
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
        if ($action == 'REPLACE') {
            $params = [];
            $sql = "REPLACE INTO {$this->formatTable($this->tablePrefix. $this->tableName)}{$this->parseInsertDatas($data, $params)}";
            $this->sql = $sql;
            $this->params = $params;
            return $sql; //REPLACE语句已完整
        } elseif ($action == 'TRUNCATE') {
            $sql = "TRUNCATE TABLE {$this->formatTable($this->tablePrefix . $this->tableName)}";
            $this->sql = $sql;
            return $sql; //TRUNCATE语句已完整
        } else {
            $sql = parent::build($action, $data, false);
        }
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
