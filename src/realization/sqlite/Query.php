<?php


namespace fize\db\realization\sqlite;

use fize\db\definition\Query as Base;

/**
 * sqlite3查询器，占位符统一为问号
 */
class Query extends Base
{
    use Feature;
}