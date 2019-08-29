<?php

namespace fize\db\realization\mysql\db;


/**
 * Mysql数据库的增强功能类
 */
trait Boost
{

    /**
     * 以替换形式添加记录，正确时返回自增ID，错误返回false
     * @param array $data 数据
     * @return int 正确时返回自增ID，错误返回false
     */
    public function replace(array $data)
    {
        $this->buildSQL("REPLACE", $data);
        $id = $this->query($this->sql, $this->params);
        return $id;
    }
	
	/**
	 * 清空记录
	 * @return bool 成功时返回true，失败时返回false
	 */
	public function truncate()
    {
		if(!empty($this->where)){
			return false; //TRUNCATE不允许有条件语句
		}
		$this->buildSQL("TRUNCATE");
		return $this->query($this->sql) === false ? false : true;
	}

    /**
     * 解析插入多条数值的SQL部分语句，用于数值原样写入
     * @param array $data_set 数据集
     * @param array $fields 可选参数$fields用于指定要插入的字段名数组，这样参数$data_set的元素数组就可以不需要指定键名，方便输入
     * @param array $params 可能要操作的参数数组
     * @return string
     */
	private function parseInsertAllDatas(array $data_set, array $fields = [], array &$params = [])
    {
		if(empty($fields)){  //$fields为空时，$data_set各元素必须带键名，且键名顺序、名称都需要一致
			foreach(array_keys($data_set[0]) as $key){
				$fields[] = $key;
			}
		}
		$values = []; //SQL各单位值填充
		foreach ($data_set as $data){
			$holdes = []; //占位符
			foreach($data as $value){
				$holdes[] = "?";
				$params[] = $value;
			}
			$values[] = '(' . implode(',', $holdes) . ')';
        }
		return '(`' . implode('`,`', $fields) . '`) VALUES ' . implode(',', $values);
	}
	
	/**
     * 批量插入记录
     * @param array $data_set 数据集
	 * @param array $fields 可选参数$fields用于指定要插入的字段名数组，这样参数$data_set的元素数组就可以不需要指定键名，方便输入
     * @return int 返回插入的记录数，错误返回false
     */
    public function insertAll(array $data_set, array $fields = null)
    {
		$params = [];
		$sql = "INSERT INTO `{$this->tablePrefix}{$this->tableName}`{$this->parseInsertAllDatas($data_set, $fields, $params)}";
		$this->sql = $sql;
		$this->params = $params;
		return $this->query($sql, $params);
    }
}