<?php

namespace fize\db\definition;


use fize\db\exception\DataNotFoundException;
use fize\db\exception\DbException;

/**
 * 数据库模型抽象类
 * @package fize\db\definition
 */
abstract class Db
{
    use Feature;

    /**
     * 是否指明为DISTINCT
     * @var bool
     */
    protected $distinct = false;

    /**
     * SQL指定要返回的字段语句
     * @var string
     */
    protected $field = "";

    /**
     * 当前数据库前缀
     * @var string
     */
    protected $tablePrefix = "";

    /**
     * 当前数据表名，不含前缀
     * @var string
     */
    protected $tableName = null;

    /**
     * ALIAS语句
     * @var string
     */
    protected $alias = "";

    /**
     * WHERE语句
     * @var string
     */
    protected $where = "";

    /**
     * WHERE语句使用的绑定参数数组
     * @var array
     */
    protected $whereParams = [];

    /**
     * GROUP语句
     * @var string
     */
    protected $group = "";

    /**
     * HAVING语句
     * @var string
     */
    protected $having = "";

    /**
     * HAVING语句使用的绑定参数数组
     * @var array
     */
    protected $havingParams = [];

    /**
     * ORDER语句
     * @var string
     */
    protected $order = "";

    /**
     * JOIN语句
     * @var string
     */
    protected $join = "";

    /**
     * UNION语句
     * @var string
     */
    protected $union = "";

    /**
     * 最后组装的SQL语句
     * @var string
     */
    protected $sql = "";

    /**
     * 语句中所有按顺序绑定的参数
     * @var array
     */
    protected $params = [];

    /**
     * @var array 缓存中的查询记录
     */
    protected static $cache_rows = [];

    /**
     * 析构函数
     */
    public function __destruct()
    {

    }

    /**
     * 返回操作对象
     * @return mixed
     */
    abstract public function prototype();

    /**
     * 待实现的安全化值
     * 由于本身存在SQL注入风险，不在业务逻辑时使用，仅供日志输出参考
     * @param mixed $value 要安全化的值
     * @return string
     */
    abstract protected function parseValue($value);

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int|null SELECT语句返回数组，INSERT/REPLACE返回自增ID，其余返回受影响行数。
     */
    abstract public function query($sql, array $params = [], callable $callback = null);

    /**
     * 开始事务
     */
    abstract public function startTrans();

    /**
     * 执行事务
     */
    abstract public function commit();

    /**
     * 回滚事务
     */
    abstract public function rollback();

    /**
     * LIMIT语句已是事实标准
     * @param int $rows 要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    abstract public function limit($rows, $offset = null);

    /**
     * 指定distinct查询
     * @param bool $distinct 为true时表示distinct
     * @return $this
     */
    public function distinct($distinct = true)
    {
        $this->distinct = $distinct;
        return $this;
    }

    /**
     * 指定要查询的字段，支持链式调用
     * @param mixed $fields 要查询的字段组成的数组或者字符串,如果需要指定别名，则使用：别名=>实际名称
     * @return $this
     */
    public function field($fields)
    {
        if (is_array($fields)) {
            $parts = [];
            foreach ($fields as $alias => $field) {
                if (is_int($alias)) {
                    $parts[] = $this->formatField($field);
                } else {
                    $parts[] = "{$this->formatField($field)} AS {$this->formatField($alias)}";
                }
            }
            $this->field = join(',', $parts);
        } else {
            $this->field = $this->formatField($fields);
        }
        return $this;
    }

    /**
     * 指定当前要操作的表,支持链式调用
     * @param string $name 表名
     * @param string $prefix 表前缀，默认为null表示使用当前前缀
     * @return $this
     */
    public function table($name, $prefix = null)
    {
        $this->tableName = $name;
        if (!is_null($prefix)) {
            $this->tablePrefix = $prefix;
        }
        return $this;
    }

    /**
     * 对当前表设置别名,支持链式调用
     * @param string $alias 别名
     * @return $this
     */
    public function alias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * GROUP语句,支持链式调用
     * @param mixed $fields 要GROUP的字段字符串或则数组
     * @return $this
     */
    public function group($fields)
    {
        if (is_array($fields)) {
            $fields = array_map([$this, 'formatField'], $fields);
            $fields = implode(',', $fields);
        }
        if (empty($this->group)) {
            $this->group = "{$fields}";
        } else {
            $this->group .= ",{$fields}";
        }
        return $this;
    }

    /**
     * 设置排序条件,支持链式调用
     * @param mixed $field_order 字符串原样，如果是数组(推荐)，则形如字段=>排序
     * @return $this
     */
    public function order($field_order)
    {
        if (is_array($field_order)) {
            foreach ($field_order as $field => $order) {
                $order = strtoupper($order);
                if (!empty($this->order)) {
                    $this->order .= ", ";
                }
                $this->order .= " {$this->formatField($field)} $order";
            }
        } else {
            if (!empty($this->order)) {
                $this->order .= ", ";
            }
            $this->order .= " {$field_order}";
        }
        $this->order = trim($this->order);
        return $this;
    }

    /**
     * 设置WHERE语句,支持链式调用
     * @param mixed $statements “Query对象”或者“查询数组”或者“WHERE子语句”，其中“WHERE子语句”支持原生的PDO问号预处理占位符;
     * @param array $parse 如果$statements是SQL预处理语句，则可以传递本参数用于预处理替换参数数组
     * @return $this
     */
    public function where($statements, array $parse = [])
    {
        /**
         * @var $Query Query
         */
        $Query = '\\' . __NAMESPACE__ . '\\Query';

        $class = '\\' . explode('\\mode\\', static::class)[0] . '\\Query';
        if(class_exists($class)) {
            $Query = $class;
        }

        if (is_array($statements)) {  // 通常情况下，我们使用简洁方式来更简便地定义条件，对于复杂条件无法满足的，可以使用查询器或者直接使用预处理语句
            /**
             * @var $query Query
             */
            $query = new $Query();
            $query->analyze($statements);
            $this->where = $query->sql();
            $this->whereParams = $query->params();
        } elseif ($statements instanceof Query) {  // $statements是查询器的情况
            $this->where = $statements->sql();
            $this->whereParams = $statements->params();
        } else {  //直接传入SQL预处理语句的情况
            $this->where = $statements;
            $this->whereParams = $parse;
        }
        return $this;
    }

    /**
     * HAVING语句，支持链式调用
     * @param mixed $statements “QueryMysql对象”或者“查询数组”或者“WHERE子语句”，其中“WHERE子语句”支持原生的PDO问号预处理占位符;
     * @param array $parse 如果$statements是SQL预处理语句，则可以传递本参数用于预处理替换参数数组
     * @return $this
     */
    public function having($statements, array $parse = [])
    {
        /**
         * @var $Query Query
         */
        $Query = '\\' . __NAMESPACE__ . '\\Query';

        $class = '\\' . explode('\\mode\\', static::class)[0] . '\\Query';
        if(class_exists($class)) {
            $Query = $class;
        }

        if (is_array($statements)) {  // 通常情况下，我们使用简洁方式来更简便地定义条件，对于复杂条件无法满足的，可以使用查询器或者直接使用预处理语句
            /**
             * @var $query Query
             */
            $query = new $Query();
            $query->analyze($statements);
            $this->having = $query->sql();
            $this->havingParams = $query->params();
        } elseif ($statements instanceof Query) {  // $statements是查询器的情况
            $this->having = $statements->sql();
            $this->havingParams = $statements->params();
        } else {  //直接传入SQL预处理语句的情况
            $this->having = $statements;
            $this->havingParams = $parse;
        }
        return $this;
    }

    /**
     * JOIN条件,可以使用所有JOIN变种,支持链式调用
     * @param mixed $table 表名，是数组时是形如别名=>表名，且只能有一个元素，否则无效
     * @param string $type JOIN形式,默认为JOIN
     * @param string $on ON条件，建议ON条件单独开来
     * @param string $using USING字段
     * @return $this
     */
    public function join($table, $type = "JOIN", $on = null, $using = null)
    {
        $ttable = '';
        if (is_array($table)) {
            if (count($table) != 1) {
                return $this;
            }
            foreach ($table as $alias => $tname) {
                $ttable = "{$this->formatTable($tname)} AS {$this->formatTable($alias)}";
            }
        } else {
            $ttable = $this->formatTable($table);
        }
        $this->join .= " {$type} {$ttable}";
        if (!is_null($on)) {
            $this->join .= " ON {$on}";
        }
        if (!is_null($using)) {
            $this->join .= " USING({$this->formatField($using)})";
        }
        return $this;
    }

    /**
     * INNER JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function innerJoin($table, $on = null)
    {
        return $this->join($table, "INNER JOIN", $on);
    }

    /**
     * LEFT JOIN条件,支持链式调用
     * @param mixed $table 表名，是数组时是形如别名=>表名，且只能有一个元素，否则无效
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function leftJoin($table, $on = null)
    {
        return $this->join($table, "LEFT JOIN", $on);
    }

    /**
     * RIGHT JOIN条件,支持链式调用
     * @param string $table 表名，可将ON条件一起带上
     * @param string $on ON条件，建议ON条件单独开来
     * @return $this
     */
    public function rightJoin($table, $on = null)
    {
        return $this->join($table, "RIGHT JOIN", $on);
    }

    /**
     * UNION语句,支持链式调用
     * @param string $sql 要UNION的SQL语句
     * @param string $union_type 类型，可选值UNION、UNION ALL、UNION DISTINCT，默认UNION
     * @return $this
     */
    public function union($sql, $union_type = "UNION")
    {
        $this->union .= " {$union_type} {$sql}";
        return $this;
    }

    /**
     * UNION ALL语句,支持链式调用
     * @param string $sql 要UNION ALL的SQL语句
     * @return $this
     */
    public function unionAll($sql)
    {
        return $this->union($sql, 'UNION ALL');
    }

    /**
     * UNION DISTINCT语句,支持链式调用
     * @param string $sql 要UNION DISTINCT的SQL语句
     * @return $this
     */
    public function unionDistinct($sql)
    {
        return $this->union($sql, 'UNION DISTINCT');
    }

    /**
     * 获取最后运行的SQL
     * 仅供日志使用的SQL语句，由于本身存在SQL危险请不要真正用于执行
     * @param bool $real 是否返回最终SQL语句而非预处理语句
     * @return string
     */
    public function getLastSql($real = false)
    {
        if ($real) {
            $temp = explode('?', $this->sql);
            $last_sql = "";
            for ($i = 0; $i < count($temp) - 1; $i++) {
                $last_sql .= $temp[$i] . $this->parseValue($this->params[$i]);
            }
            $last_sql .= $temp[count($temp) - 1];
            return $last_sql;
        } else {
            return $this->sql;
        }
    }

    /**
     * 解析插入数值的SQL部分语句，用于数值原样写入
     * @param array $datas 要写入的数值数组
     * @param array $params 可能要操作的参数数组
     * @return string
     */
    protected function parseInsertDatas(array $datas, array &$params = [])
    {
        $fields = []; //字段名
        $holdes = []; //占位符
        foreach ($datas as $key => $val) {
            $fields[] = $this->formatField($key);
            if (is_array($val)) {  //传递数组则认为是原值写入(添加时应该没有这样的使用)
                $holdes[] = $val[0];
            } else {
                $holdes[] = "?";
                $params[] = $val;
            }
        }
        return '(' . implode(',', $fields) . ') VALUES (' . implode(',', $holdes) . ')';
    }

    /**
     * 解析更新数值的SQL部分语句，用于数值原样更新
     * @param array $datas 要更新的数值数组
     * @param array $params 可能要操作的参数数组
     * @return string
     */
    protected function parseUpdateDatas(array $datas, array &$params = [])
    {
        $out = "";
        foreach ($datas as $key => $val) {
            if (!empty($out)) {
                $out .= " , ";
            }
            if (is_array($val)) {  //传递数组则认为是原值写入
                $out .= "{$this->formatField($key)} = {$val[0]}";
            } else {
                $out .= "{$this->formatField($key)} = ?";
                $params[] = $val;
            }
        }
        return $out;
    }

    /**
     * 清空当前条件，以便于下次查询
     * 子类可根据需要进行重写
     */
    protected function clear()
    {
        //以下注释请不要删除，用于提示不需要重置的条件
        //$this->tablePrefix = "";
        //$this->tableName = "";
        //$this->sql = "";
        //$this->params = [];

        //清空一次性条件
        $this->alias = "";
        $this->join = "";
        $this->where = "";
        $this->group = "";
        $this->having = "";
        $this->field = "";
        $this->order = "";
        $this->union = "";
        $this->whereParams = [];
        $this->havingParams = [];
    }

    /**
     * 根据当前条件构建SQL语句
     * 子类可根据需要进行重写
     * @param string $action SQL语句类型
     * @param array $data 可能需要的数据
     * @param bool $clear 是否清理当前条件，默认true
     * @return string 最后组装的SQL语句
     * @throws DbException
     */
    protected function build($action, array $data = [], $clear = true)
    {
        switch ($action) {
            case "DELETE" : //删除
                $sql = "DELETE FROM {$this->formatTable($this->tablePrefix. $this->tableName)}";
                $this->params = $this->whereParams + $this->havingParams;
                break;
            case "INSERT" : //添加
                $params = [];
                $sql = "INSERT INTO {$this->formatTable($this->tablePrefix. $this->tableName)}{$this->parseInsertDatas($data, $params)}";
                $this->params = $params;
                break;
            case "SELECT" : //查询
                if (empty($this->field)) {
                    $this->field = "*";
                }
                $sql = "SELECT {$this->field} FROM {$this->formatTable($this->tablePrefix. $this->tableName)}";
                $this->params = $this->whereParams + $this->havingParams;
                break;
            case "UPDATE" : //更新
                $data_params = [];
                $sql = "UPDATE {$this->formatTable($this->tablePrefix. $this->tableName)} SET {$this->parseUpdateDatas($data, $data_params)}";
                $this->params = array_merge($data_params, $this->whereParams, $this->havingParams);
                break;
            default :
                //仅需要支持DELETE、INSERT、REPLACE、SELECT、UPDATE，防止其他语句进入
                throw new DbException("Illegal SQL statement: {$action}");
        }
        if (in_array($action, ['DELETE', 'SELECT', 'UPDATE'])) {
            if (!empty($this->alias)) {
                $sql .= " AS {$this->formatField($this->alias)}";
            }
            if (!empty($this->join)) {
                $sql .= " {$this->join}";
            }
            if (!empty($this->where)) {
                $sql .= " WHERE {$this->where}";
            }
            if (!empty($this->group)) {
                $sql .= " GROUP BY {$this->group}";
            }
            if (!empty($this->having)) {
                $sql .= " HAVING {$this->having}";
            }
            $sql .= $this->union;
            if (!empty($this->order)) {
                $sql .= " ORDER BY {$this->order}";
            }
        }
        $this->sql = $sql;
        if ($clear) {
            $this->clear();
        }
        return $sql;
    }

    /**
     * 插入记录，正确时返回自增ID，错误返回false
     * @param array $data 数据
     * @return int 返回自增ID
     */
    public function insert(array $data)
    {
        $this->build("INSERT", $data);
        return $this->query($this->sql, $this->params);
    }

    /**
     * 遍历当前结果集
     * 由于少了一层循环和转化，fetch方法比select性能上略有提升，但不方便外部调用，特别是MVC等架构
     * @param callable $func 遍历函数
     */
    public function fetch(callable $func)
    {
        $this->build("SELECT");
        $this->query($this->sql, $this->params, $func);
    }

    /**
     * 删除记录
     * @return int 返回受影响记录条数
     */
    public function delete()
    {
        $this->build("DELETE");
        return $this->query($this->sql, $this->params);
    }

    /**
     * 更新记录
     * @param array $data 要设置的数据
     * @return int 返回受影响记录条数
     */
    public function update($data)
    {
        $this->build('UPDATE', $data);
        return $this->query($this->sql, $this->params);
    }

    /**
     * 执行查询，返回结果记录列表
     * @param bool $cache 是否使用搜索缓存，默认true
     * @return array
     */
    public function select($cache = true)
    {
        $this->build("SELECT");
        if ($cache) {
            $sql = $this->getLastSql(true);
            if (!isset(self::$cache_rows[$sql])) {
                self::$cache_rows[$sql] = $this->query($this->sql, $this->params);
            }
            return self::$cache_rows[$sql];
        }
        return $this->query($this->sql, $this->params);
    }

    /**
     * 执行查询，获取单条记录
     * @param bool $cache 是否使用搜索缓存，默认false
     * @return array 如果无记录则返回null
     */
    public function findOrNull($cache = false)
    {
        $rows = $this->limit(1, 0)->select($cache);
        if (count($rows) == 0) {
            return null;
        }
        return $rows[0];
    }

    /**
     * 执行查询，获取单条记录,如果未找到则抛出错误
     * @param bool $cache 是否使用搜索缓存，默认false
     * @return array
     * @throws DataNotFoundException
     */
    public function find($cache = false)
    {
        $row = $this->findOrNull($cache);
        if (empty($row)) {
            throw new DataNotFoundException('Recordset Not Found', 0, $this->getLastSql(true));
        }
        return $row;
    }

    /**
     * 得到某个字段的值
     * @access public
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @param bool $force 强制转为数字类型
     * @return mixed 如果$force为true时真返回数字类型
     */
    public function value($field, $default = null, $force = false)
    {
        $this->field([$field]);
        $row = $this->findOrNull();
        $result = $default;
        if (!empty($row)) {
            $result = array_values($row)[0];  //第一列第一个值
            if ($force) {
                $result = is_numeric($result) ? $result + 0 : $result;
            }
        }
        return $result;
    }

    /**
     * 得到某个列的数组
     * @param string $field 字段名
     * @return array
     */
    public function column($field)
    {
        $this->field($field);
        $values = [];
        $this->fetch(function ($row) use ($field, &$values) {
            $values[] = $row[$field];
        });
        return $values;
    }

    /**
     * 使用模拟的LIMIT语句进行简易分页,支持链式调用
     * @param int $index 页码
     * @param int $prepg 每页记录数量
     * @return $this
     */
    public function page($index, $prepg = 10)
    {
        $rows = $prepg;
        $offset = ($index - 1) * $prepg;
        return $this->limit($rows, $offset);
    }

    /**
     * COUNT查询
     * @param string $field 字段名
     * @return int
     */
    public function count($field = "*")
    {
        return $this->value("COUNT({$this->formatField($field)})", 0, true);
    }

    /**
     * SUM查询
     * @param string $field 字段名
     * @return int
     */
    public function sum($field)
    {
        $sum = $this->value("SUM({$this->formatField($field)})", 0, true);
        if (is_null($sum)) { //求SUM时，并不希望得到NULL值
            $sum = 0;
        }
        return $sum;
    }

    /**
     * MIN查询
     * @param string $field 字段名
     * @param bool $force 强制转为数字类型
     * @return mixed 如果$force为true时真返回数字类型
     */
    public function min($field, $force = true)
    {
        return $this->value("MIN({$this->formatField($field)})", null, $force);
    }

    /**
     * MAX查询
     * @param string $field 字段名
     * @param bool $force 强制转为数字类型
     * @return mixed 如果$force为true时真返回数字类型
     */
    public function max($field, $force = true)
    {
        return $this->value("MAX({$this->formatField($field)})", null, $force);
    }

    /**
     * AVG查询
     * @param string $field 字段名
     * @return mixed
     */
    public function avg($field)
    {
        return $this->value("AVG({$this->formatField($field)})", 0, true);
    }

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
        $data = [$field => ["{$this->formatField($field)} + {$step}"]];
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
        $data = [$field => ["{$this->formatField($field)} - {$step}"]];
        return $this->update($data);
    }

    /**
     * 完整分页，执行该方法可以获取到分页记录、完整记录数、总页数，可用于分页输出
     * @param int $page 页码
     * @param int $size 每页记录数量，默认每页10个
     * @return array [记录个数, 总页数、记录数组]
     * @todo 寻找更好的方案
     */
    public function paginate($page, $size = 10)
    {
        $sql_temp = $this->build("SELECT", [], false);
        //var_dump($sql_temp);
        $sql_for_count = substr_replace($sql_temp, "COUNT(*)", 7, strlen($this->field));
        if (!empty($this->order)) {  //消除ORDER BY 语句对COUNT语句的影响问题
            $sql_for_count = str_replace(" ORDER BY {$this->order}", "", $sql_for_count);
        }
        //var_dump($sql_for_count);
        $rows_for_count = $this->query($sql_for_count, $this->params);
        $count = (int)array_values($rows_for_count[0])[0];  //第一列第一个值
        $this->page($page, $size);
        $this->build("SELECT");
        //var_dump($this->_sql);
        $result = $this->query($this->sql, $this->params);
        return [
            $count,  //记录个数
            (int)ceil($count / $size),  //总页数
            $result  //当前返回的分页记录数组
        ];
    }

    /**
     * 批量插入记录
     * @param array $data_sets 数据集
     * @param array $fields 可选参数$fields用于指定要插入的字段名数组，这样参数$data_set的元素数组就可以不需要指定键名，方便输入
     * @return int 返回插入成功的记录数
     */
    public function insertAll(array $data_sets, array $fields = null)
    {
        if($fields) {
            $datas = [];
            foreach ($data_sets as $data_set) {
                $data = [];
                foreach ($fields as $index => $field) {
                    $data[$field] = $data_set[$index];
                }
                $datas[] = $data;
            }
        } else {
            $datas = $data_sets;
        }

        $count = 0;
        foreach ($datas as $data) {
            $result = $this->insert($data);
            if($result !== false) {
                $count++;
            }
        }
        return $count;
    }
}