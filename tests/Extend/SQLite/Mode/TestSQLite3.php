<?php

namespace Tests\Extend\SQLite\Mode;

use Fize\Database\Extend\SQLite\Mode\SQLite3;
use PHPUnit\Framework\TestCase;

class TestSQLite3 extends TestCase
{

    public function test__construct()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');
        var_dump($db);
        self::assertIsObject($db);
    }

    public function test__destruct()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');
        var_dump($db);
        unset($db);
        self::assertTrue(true);
    }

    public function testLastInsertId()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');
        $data = [
            'name'     => "!乱/七\八'糟\"的*字?符%串`一#大@堆(",
            'add_time' => time()
        ];
        $db->table('user')->insert($data);
        $sql = $db->getLastSql(true);
        var_dump($sql);
        $id = $db->lastInsertId();
        var_dump($id);
        self::assertIsNumeric($id);
    }

    public function testPrototype()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');
        $prototype = $db->prototype();
        var_dump($prototype);
        self::assertIsObject($prototype);
    }

    public function testQuery()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');

        //查询
        $sql = 'SELECT * FROM "user" WHERE name = ?';
        $rows = $db->query($sql, ['测试2']);
        var_dump($rows);
        self::assertIsArray($rows);
    }

    public function testExecute()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');

        //插入
        $sql = 'INSERT INTO "user" ("name","sex","add_time") VALUES(?, ?, ?)';
        $result = $db->execute($sql, ['测试2', 5, 123456]);
        var_dump($result);
        self::assertEquals(1, $result);

        //更新
        $sql = 'UPDATE "user" SET name = ? WHERE id = ?';
        $num = $db->execute($sql, ['这是我想要写入的东西123！！',  8]);
        var_dump($num);
        self::assertEquals(1, $num);
    }

    public function testStartTrans()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');
        $db->startTrans();
        $db->commit();
        self::assertTrue(true);
    }

    public function testCommit()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');

        $db->startTrans();

        $sql = 'UPDATE "user" SET "name" = ? WHERE id = 7';
        $num = $db->execute($sql, ["陈峰展1631"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->commit();

        $sql = 'SELECT * FROM "user" WHERE id = 7';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals('陈峰展1631', $rows[0]['name']);
    }

    public function testRollback()
    {
        $db = new SQLite3('F:\data\sqlite3\gm_test.sqlite3');

        $db->startTrans();

        $sql = 'UPDATE "user" SET "name" = ? WHERE id = 7';
        $num = $db->execute($sql, ["陈峰展16312"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->rollback();

        $sql = 'SELECT * FROM "user" WHERE id = 7';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals('陈峰展1631', $rows[0]['name']);
    }
}
