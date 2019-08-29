<?php

namespace fize\db\realization\mssql\db;

/**
 * Mssql数据库的增强功能类
 */
trait Boost
{

    /**
     * 是否支持新特性
     * 自MSSQL2012开始支持“OFFSET 1 ROWS FETCH NEXT 3 ROWS ONLY”语句
     * @var bool
     */
    protected $new_feature = false;

    /**
     * 设置是否支持新特性
     * @param $bool
     */
    public function newFeature($bool)
    {
        $this->new_feature = $bool;
    }

    /**
     * 完整分页，执行该方法可以获取到分页记录、完整记录数、总页数，可用于分页输出
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