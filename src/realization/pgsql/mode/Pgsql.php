<?php


namespace fize\db\realization\pgsql\mode;

use fize\db\realization\pgsql\Db;
use fize\db\realization\pgsql\mode\driver\Pgsql as Driver;

/**
 * Class Pgsql
 * @todo 待完成
 * @package fize\db\realization\pgsql\mode
 */
class Pgsql extends Db
{

    /**
     * 使用的Pgsql驱动对象
     * @var Driver
     */
    protected $driver = null;
}
