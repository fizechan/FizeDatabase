<?php

namespace fize\db\realization\mysql;


use fize\db\definition\Db as Base;
use fize\db\realization\mysql\db\Unit;
use fize\db\realization\mysql\db\Join;
use fize\db\realization\mysql\db\Build;
use fize\db\realization\mysql\db\Search;
use fize\db\realization\mysql\db\Boost;


/**
 * MYSQL的ORM模型
 */
abstract class Db extends Base
{
    use Feature;
    use Unit;
    use Join;
    use Build;
    use Search;
    use Boost;
}