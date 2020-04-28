<?php

namespace extend\oracle\mode;

use fize\db\extend\oracle\mode\Pdo;
use PHPUnit\Framework\TestCase;

class TestPdo extends TestCase
{

    public function test__destruct()
    {
        $db = new Pdo('127.0.0.1', "OT", "Orcl123456", "gmtest", null, 'UTF8');
        $rows = $db->query('SELECT * FROM BIND_EXAMPLE_2');
        var_dump($rows);
        self::assertIsArray($rows);
        var_dump($db);
        self::assertIsObject($db);
    }

    public function test__construct()
    {
        $db = new Pdo('127.0.0.1', "OT", "Orcl123456", "gmtest", null, 'UTF8');
        $rows = $db->query('SELECT * FROM BIND_EXAMPLE_2');
        var_dump($rows);
        $db = null;
        self::assertTrue(true);
    }
}
