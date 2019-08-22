<?php

namespace fize\db\realization\access;

use fize\db\definition\Db as Base;
use fize\db\realization\access\db\Unit;
use fize\db\realization\access\db\Search;
use fize\db\realization\access\db\Boost;


/**
 * Access数据库类
 */
abstract class Db extends Base
{
    use Feature;
    use Unit;
    use Search;
    use Boost;
}