<?php


namespace fize\db\realization\pgsql\mode;

use fize\db\realization\pgsql\Db;
use fize\db\realization\pgsql\mode\driver\Pgsql as Driver;


class Pgsql extends Db
{

    /**
     * 使用的Pgsql驱动对象
     * @var Driver
     */
    protected $driver = null;
}
