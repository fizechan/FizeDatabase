<?php

namespace Fize\Database\Extend\Oracle;

use Fize\Database\Core\Query as CoreQuery;

/**
 * 查询器
 *
 * Oracle查询器，占位符统一为问号
 */
class Query extends CoreQuery
{
    use Feature;
}
