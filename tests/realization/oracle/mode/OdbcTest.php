<?php

namespace realization\oracle\mode;

use fize\db\realization\oracle\mode\Odbc;
use PHPUnit\Framework\TestCase;

class OdbcTest extends TestCase
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