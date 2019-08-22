<?php
namespace fize\db\realization\mssql;


use fize\db\definition\Query as Base;

/**
 * MSSQL查询器，占位符统一为问号
 */
class Query extends Base
{
    use Feature;
}