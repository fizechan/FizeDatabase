<?php

namespace extend\oracle\mode;

use fize\database\extend\oracle\mode\Odbc;
use PHPUnit\Framework\TestCase;

class TestOdbc extends TestCase
{

    public function test__construct()
    {
        $db = new Odbc("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        var_dump($db);
    }

    public function test__destruct()
    {

    }

    public function testQuery()
    {

    }
}
