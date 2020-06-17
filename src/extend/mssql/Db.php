<?php

namespace fize\database\extend\mssql;


use fize\database\core\Db as Base;


/**
 * 数据库
 */
abstract class Db extends Base
{
    use Feature;

    /**
     * @var int 指定每页记录集数量，为null时表示不指定，全部返回。
     */
    protected $size = null;

    /**
     * @var int 指定游标指针位移，为null时不指定，不移动。
     */
    protected $offset = null;

    /**
     * 是否支持新特性
     * 自MSSQL2012开始支持“OFFSET 1 ROWS FETCH NEXT 3 ROWS ONLY”语句
     * @var bool
     */
    protected $new_feature = true;

    /**
     * 自己实现的安全化值
     * @param mixed $value 要安全化的值
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = "'" . str_replace("'", "''", $value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'NULL';
        }
        return $value;
    }

    /**
     * @param bool $bool 设置是否支持新特性
     */
    public function newFeature($bool)
    {
        $this->new_feature = $bool;
    }

    /**
     * 设置TOP,支持链式调用
     * @param int $rows 要返回的记录数
     * @return $this
     */
    public function top($rows)
    {
        $this->size = $rows;
        return $this;
    }

    /**
     * 模拟MySQL的LIMIT语句,支持链式调用
     * @param int $rows   要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    public function limit($rows, $offset = null)
    {
        $this->size = $rows;
        $this->offset = $offset;
        return $this;
    }

    /**
     * CROSS JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function crossJoin($table, $on = null)
    {
        return $this->join($table, "CROSS JOIN", $on);
    }

    /**
     * FULL JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function fullJoin($table, $on = null)
    {
        return $this->join($table, "FULL JOIN", $on);
    }

    /**
     * LEFT OUTER JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function leftOuterJoin($table, $on = null)
    {
        return $this->join($table, "LEFT OUTER JOIN", $on);
    }

    /**
     * RIGHT OUTER JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on    ON条件，建议ON条件单独开来
     * @return $this
     */
    public function rightOuterJoin($table, $on = null)
    {
        return $this->join($table, "RIGHT OUTER JOIN", $on);
    }

    /**
     * 根据当前条件构建SQL语句
     * @param string $action SQL语句类型
     * @param array  $data   可能需要的数据
     * @param bool   $clear  是否清理当前条件，默认true
     * @return string 最后组装的SQL语句
     */
    protected function build($action, array $data = [], $clear = true)
    {
        if ($action == 'TRUNCATE') {
            $sql = "TRUNCATE TABLE {$this->formatTable($this->tablePrefix . $this->tableName)}";
            $this->sql = $sql;
            if ($clear) {
                $this->clear();
            }
            return $sql; //TRUNCATE语句已完整
        }

        if ($action == 'SELECT') {
            if (!is_null($this->size)) {
                if (empty($this->offset)) {
                    $sql = parent::build($action, $data, false);
                    $sql = substr_replace($sql, " TOP {$this->size}", 6, 0);
                    $this->sql = $sql;
                } else {
                    if ($this->new_feature) {  //自MSSQL2012开始支持“OFFSET 1 ROWS FETCH NEXT 3 ROWS ONLY”语句
                        $sql = parent::build($action, $data, false);
                        if (empty($this->order)) {  //OFFSET 需要与ORDER BY语句一起使用，如果没有指定ORDER BY 则使用RAND()
                            $sql .= " ORDER BY RAND() OFFSET {$this->offset} ROWS FETCH NEXT {$this->size} ROWS ONLY";
                        } else {
                            $sql .= " OFFSET {$this->offset} ROWS FETCH NEXT {$this->size} ROWS ONLY";
                        }
                    } else {  //进行偏移量移动的旧版SQL组装
                        if (empty($this->field)) {
                            $this->field = "*";
                        }
                        if (empty($this->order)) {
                            $order = "ORDER BY RAND()";
                        } else {
                            $order = "ORDER BY " . $this->order;
                        }
                        $this->order = '';  //消除子语句使用ORDER BY语句的错误
                        $this->field .= ", ROW_NUMBER() OVER ({$order}) AS _RN_";

                        $sql = parent::build($action, $data, false);
                        $top_full = $this->size + $this->offset;
                        $sql = substr_replace($sql, " TOP {$top_full}", 6, 0);
                        $sql = "SELECT TOP {$this->size} * FROM ({$sql}) AS _TT_ WHERE _TT_._RN_ > {$this->offset}";
                    }
                    $this->sql = $sql;
                }
                if ($clear) {
                    $this->clear();
                }
                return $sql;
            }
        }

        return parent::build($action, $data, $clear);
    }

    /**
     * 执行查询，返回结果记录列表
     * @param bool $cache 是否使用搜索缓存，默认true
     * @return array
     */
    public function select($cache = true)
    {
        $rows = parent::select($cache);
        if (!$this->new_feature) {
            $temp_rows = [];
            foreach ($rows as $row) {
                unset($row['_RN_']);
                unset($row['_rn_']);
                $temp_rows[] = $row;
            }
            return $temp_rows;
        }
        return $rows;
    }

    /**
     * 完整分页，执行该方法可以获取到分页记录、完整记录数、总页数，可用于分页输出
     *
     * 针对MSSQL的再处理，删除非必要的中间字段
     * @param int $page 页码
     * @param int $size 每页记录数量，默认每页10个
     * @return array 数组键名为count、pages、rows
     */
    public function paginate($page, $size = 10)
    {
        $result = parent::paginate($page, $size);
        if (!$this->new_feature) {
            $rows = [];
            foreach ($result['rows'] as $row) {
                unset($row['_RN_']);
                unset($row['_rn_']);
                $rows[] = $row;
            }
            $result['rows'] = $rows;
        }
        return $result;
    }
}
