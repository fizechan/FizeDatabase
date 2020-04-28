<?php

namespace fize\db\extend\oracle;

use fize\db\core\Query as Base;

/**
 * 查询器
 *
 * Oracle查询器，占位符统一为问号
 */
class Query extends Base
{
    use Feature;
}
