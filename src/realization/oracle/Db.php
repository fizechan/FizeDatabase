<?php


namespace fize\db\realization\oracle;


use fize\db\definition\Db as Base;
use fize\db\realization\oracle\db\Join;
use fize\db\realization\oracle\db\Build;
use fize\db\realization\oracle\db\Search;
use fize\db\realization\oracle\db\Boost;


/**
 * Oracle的ORM模型
 */
abstract class Db extends Base
{
    use Feature;
    use Join;
    use Build;
    use Search;
    use Boost;
}