<?php

namespace fize\db\realization\mssql;


use fize\db\definition\Db as Base;
use fize\db\realization\mssql\db\Unit;
use fize\db\realization\mssql\db\Join;
use fize\db\realization\mssql\db\Build;
use fize\db\realization\mssql\db\Search;
use fize\db\realization\mssql\db\Boost;


/**
 * MSSQL的ORM模型
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