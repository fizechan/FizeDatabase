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
        self::assertIsObject($db);
    }



    public function test__destruct()
    {
        $db = new Odbc('192.168.56.101', 'root', '123456', 'gmtest');
        var_dump($db);
        unset($db);
        self::assertTrue(true);
    }

    public function testQuery()
    {
        $db = new Odbc('192.168.56.101', 'root', '123456', 'gmtest');

        //插入
        $sql = 'INSERT INTO "user" VALUES(?, ?, ?, ?)';
        $result = $db->query($sql, [23, '测试2', 5, 123456]);
        var_dump($result);
        self::assertEquals($result, 0);

       //查询
        $sql = 'SELECT * FROM "user" WHERE name = ?';
        $rows = $db->query($sql, ['陈峰展']);
        var_dump($rows);
        self::assertIsArray($rows);

        //更新
        $sql = 'UPDATE "user" SET name = ? WHERE id = ?';
        $num = $db->query($sql, ['这是我想要写入的东西123！！',  8]);
        var_dump($num);
        self::assertEquals($num, 1);
    }
}
