<?php

namespace Tests\Extend\Oracle\Mode;

use Fize\Database\extend\Oracle\mode\ODBCMode;
use PHPUnit\Framework\TestCase;

class TestODBCMode extends TestCase
{

    public function test__construct()
    {
        $db = new ODBCMode("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        var_dump($db);
    }

    public function test__destruct()
    {

    }

    public function testQuery()
    {

    }
}
