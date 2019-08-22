<?php

namespace fize\db\realization\pgsql;


use fize\db\definition\Db as Base;
use fize\db\realization\pgsql\db\Unit;
use fize\db\realization\pgsql\db\Join;
use fize\db\realization\pgsql\db\Build;
use fize\db\realization\pgsql\db\Search;
use fize\db\realization\pgsql\db\Boost;


/**
 * PostgreSQL的ORM模型
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
