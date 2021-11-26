<?php

namespace Tests\Extend\PgSQL\Mode;

use Fize\Database\Extend\PgSQL\Mode\ODBC;
use PHPUnit\Framework\TestCase;

class TestODBC extends TestCase
{

    public function test__construct()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');
        var_dump($db);
        self::assertIsObject($db);
    }

    public function test__destruct()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');
        var_dump($db);
        unset($db);
        self::assertTrue(true);
    }

    public function testLastInsertId()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');
        $data = [
            'name'     => "!乱/七\八'糟\"的*字?符%串`一#大@堆(",
            'add_time' => time()
        ];
        $db->table('user')->insert($data);
        $sql = $db->getLastSql(true);
        var_dump($sql);
        $id = $db->lastInsertId('event_id_seq');
        var_dump($id);
        self::assertIsNumeric($id);
    }

    public function testPrototype()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');
        $prototype = $db->prototype();
        var_dump($prototype);
        self::assertIsObject($prototype);
    }

    public function testQuery()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');

        //插入
        $sql = 'INSERT INTO "user" ("name","sex","add_time") VALUES(?, ?, ?)';
        $result = $db->query($sql, ['测试2', 5, 123456]);
        var_dump($result);
        self::assertEquals($result, 1);

       //查询
        $sql = 'SELECT * FROM "user" WHERE name = ?';
        $rows = $db->query($sql, ['陈峰展']);
        var_dump($rows);
        self::assertIsArray($rows);

        //更新
        $sql = 'UPDATE "user" SET name = ? WHERE id = ?';
        $num = $db->query($sql, ['这是我想要写入的东西123！！',  13]);
        var_dump($num);
        self::assertEquals($num, 1);
    }

    public function testStartTrans()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');
        $db->startTrans();
        $db->commit();
        self::assertTrue(true);
    }

    public function testCommit()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');

        $db->startTrans();

        $sql = 'UPDATE "user" SET "name" = ? WHERE id = 101';
        $num = $db->query($sql, ["陈峰展1631"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->commit();

        $sql = 'SELECT * FROM "user" WHERE id = 101';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals($rows[0]['name'], '陈峰展1631');
    }

    public function testRollback()
    {
        $db = new ODBC('192.168.56.101', 'root', '123456', 'gmtest');

        $db->startTrans();

        $sql = 'UPDATE "user" SET "name" = ? WHERE id = 101';
        $num = $db->query($sql, ["陈峰展16312"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->rollback();

        $sql = 'SELECT * FROM "user" WHERE id = 101';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals($rows[0]['name'], '陈峰展1631');
    }
}
