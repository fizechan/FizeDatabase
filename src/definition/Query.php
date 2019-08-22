<?php


namespace fize\db\definition;

use fize\db\definition\query\Build;
use fize\db\definition\query\Basic;
use fize\db\definition\query\Compare;
use fize\db\definition\query\Range;
use fize\db\definition\query\Analyze;
use fize\db\definition\query\Compose;

/**
 * 条件查询器，占位符统一为问号
 */
class Query
{
    use Feature;
    use Build;
    use Basic;
    use Compare;
    use Range;
    use Analyze;
    use Compose;
}