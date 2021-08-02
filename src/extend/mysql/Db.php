<?php

namespace fize\database\extend\mysql;


use fize\database\core\Db as CoreDb;

/**
 * 数据库
 */
abstract class Db extends CoreDb
{
    use Feature;

    /**
     * @var string LIMIT语句
     */
    protected $limit = "";

    /**
     * @var bool 本次查询是否启用LOCK锁
     */
    protected $lock = false;

    /**
     * @var string LOCK语句主体
     */
    protected $lock_sql = "";

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
     * 指定查询lock
     * @param bool  $lock      是否启用LOCK语句
     * @param array $lock_sqls 表锁定语句快，支持多个，默认为启用当前表的写锁定
     * @return $this
     * @todo 写法不是很好，需要改进
     */
    public function lock(bool $lock = true, array $lock_sqls = null)
    {
        $this->lock = $lock;
        if ($this->lock) {
            if (is_null($lock_sqls)) {
                $lock_sqls = ["{$this->formatTable($this->tablePrefix. $this->tableName)}` WRITE"];
            }
            $this->lock_sql = implode(", ", $lock_sqls);
        } else {
            $this->lock_sql = "";
        }
        return $this;
    }

    /**
     * CROSS JOIN条件,支持链式调用
     * @param string      $table 表名，可将ON条件一起带上
     * @param string|null $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function crossJoin(string $table, string $on = null): Db
    {
        return $this->join($table, "CROSS JOIN", $on);
    }

    /**
     * LEFT OUTER JOIN条件,支持链式调用
     * @param string      $table 表名，可将ON条件一起带上
     * @param string|null $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function leftOuterJoin(string $table, string $on = null): Db
    {
        return $this->join($table, "LEFT OUTER JOIN", $on);
    }

    /**
     * RIGHT OUTER JOIN条件,支持链式调用
     * @param string      $table 表名，可将ON条件一起带上
     * @param string|null $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function rightOuterJoin(string $table, string $on = null): Db
    {
        return $this->join($table, "RIGHT OUTER JOIN", $on);
    }

    /**
     * STRAIGHT_JOIN条件，非标准SQL语句，不建议使用,支持链式调用
     * @param string      $table 表名，可将ON条件一起带上
     * @param string|null $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function straightJoin(string $table, string $on = null): Db
    {
        return $this->join($table, "STRAIGHT_JOIN", $on);
    }

    /**
     * 清空当前条件，以便于下次查询
     */
    protected function clear()
    {
        parent::clear();
        $this->limit = "";
        $this->lock = false;
        $this->lock_sql = "";
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
            $sql .= " LIMIT {$this->limit}";
        }
        $this->sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }

    /**
     * 以替换形式添加记录
     * @param array $data 数据
     * @return int 返回自增ID
     */
    public function replace(array $data): int
    {
        $this->build("REPLACE", $data);
        $this->execute($this->sql, $this->params);
        return $this->lastInsertId();
    }

    /**
     * 清空记录
     * @return int 返回受影响记录数
     */
    public function truncate()
    {
        if (!empty($this->where)) {
            return false; //TRUNCATE不允许有条件语句
        }
        $this->build("TRUNCATE");
        return $this->execute($this->sql);
    }

    /**
     * 完整分页
     *
     * 执行该方法可以获取到分页记录、完整记录数、总页数，可用于分页输出
     * @param int $page 页码
     * @param int $size 每页记录数量
     * @return array [记录个数, 记录数组, 总页数]
     */
    public function paginate(int $page, int $size = 10): array
    {
        $this->page($page, $size);
        if (empty($this->field)) {
            $this->field = '*';
        }
        $this->field = 'SQL_CALC_FOUND_ROWS ' . $this->field;
        $this->build("SELECT");
        $rows = $this->query($this->sql, $this->params);
        $count = ($this->query("SELECT FOUND_ROWS() AS total"))[0]['total'];
        $count = (int)$count;
        return [
            $count,
            $rows,
            (int)ceil($count / $size)
        ];
    }

    /**
     * 解析插入多条数值的SQL部分语句，用于数值原样写入
     * @param array $data_sets 数据集
     * @param array $fields    可选参数$fields用于指定要插入的字段名数组，这样参数$data_set的元素数组就可以不需要指定键名，方便输入
     * @param array $params    可能要操作的参数数组
     * @return string
     */
    private function parseInsertAllDatas(array $data_sets, array $fields = [], array &$params = []): string
    {
        if (empty($fields)) {  //$fields为空时，$data_set各元素必须带键名，且键名顺序、名称都需要一致
            foreach (array_keys($data_sets[0]) as $key) {
                $fields[] = $key;
            }
        }
        $values = []; //SQL各单位值填充
        foreach ($data_sets as $data_set) {
            $holdes = []; //占位符
            foreach ($data_set as $value) {
                $holdes[] = "?";
                $params[] = $value;
            }
            $values[] = '(' . implode(',', $holdes) . ')';
        }
        return '(`' . implode('`,`', $fields) . '`) VALUES ' . implode(',', $values);
    }

    /**
     * 批量插入记录
     * @param array $data_sets 数据集
     * @param array $fields    可选参数$fields用于指定要插入的字段名数组，这样参数$data_set的元素数组就可以不需要指定键名，方便输入
     * @return int 返回插入的记录数
     */
    public function insertAll(array $data_sets, array $fields = null): int
    {
        $params = [];
        $sql = "INSERT INTO `{$this->tablePrefix}{$this->tableName}`{$this->parseInsertAllDatas($data_sets, $fields, $params)}";
        $this->sql = $sql;
        $this->params = $params;
        return $this->execute($sql, $params);
    }
}
