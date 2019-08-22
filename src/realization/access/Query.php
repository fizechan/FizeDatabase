<?php
namespace fize\db\realization\access;


use fize\db\definition\Query as Base;

/**
 * ACCESS查询器，占位符统一为问号
 */
class Query extends Base
{
    use Feature;
}