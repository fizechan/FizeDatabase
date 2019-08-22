<?php

namespace fize\db\realization\sqlite;


use fize\db\definition\Db as Base;
use fize\db\realization\sqlite\db\Unit;
use fize\db\realization\sqlite\db\Build;
use fize\db\realization\sqlite\db\Search;
use fize\db\realization\sqlite\db\Boost;


/**
 * Sqlite的ORM模型
 */
abstract class Db extends Base
{
    use Feature;
    use Unit;
    use Build;
    use Search;
    use Boost;
}