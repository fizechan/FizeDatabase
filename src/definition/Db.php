<?php


namespace fize\db\definition;

use fize\db\definition\db\Unit;
use fize\db\definition\db\Join;
use fize\db\definition\db\Union;
use fize\db\definition\db\Build;
use fize\db\definition\db\Basic;
use fize\db\definition\db\Search;
use fize\db\definition\db\Calculation;
use fize\db\definition\db\Update;
use fize\db\definition\db\Boost;

/**
 * 数据库模型抽象类
 */
abstract class Db
{
    use Feature;
    use Unit;
    use Join;
    use Union;
    use Build;
    use Basic;
    use Search;
    use Calculation;
    use Update;
    use Boost;
}