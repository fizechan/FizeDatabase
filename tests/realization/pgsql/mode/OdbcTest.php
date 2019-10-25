<?php

namespace realization\pgsql\mode;

use fize\db\realization\pgsql\mode\Odbc;
use PHPUnit\Framework\TestCase;

class OdbcTest extends TestCase
{

    public function test__construct()
    {
        $db = new Odbc('192.168.56.101', 'root', '123456', 'gmtest');
        var_dump($db);
    }



    public function test__destruct()
    {

    }

    public function testQuery()
    {

    }


}
