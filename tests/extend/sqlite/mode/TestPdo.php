<?php

namespace extend\sqlite\mode;

use fize\db\extend\sqlite\mode\Pdo;
use PHPUnit\Framework\TestCase;

class TestPdo extends TestCase
{

    public function test__construct()
    {
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');
        var_dump($db);
        self::assertIsObject($db);
    }

    public function test__destruct()
    {
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');
        var_dump($db);
        unset($db);
        self::assertTrue(true);
    }

    public function testLastInsertId()
    {
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');
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
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');
        $prototype = $db->prototype();
        var_dump($prototype);
        self::assertIsObject($prototype);
    }

    public function testQuery()
    {
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');

        //插入
        $sql = 'INSERT INTO "user" ("name","sex","add_time") VALUES(?, ?, ?)';
        $result = $db->query($sql, ['测试2', 5, 123456]);
        var_dump($result);
        self::assertEquals($result, 1);

        //查询
        $sql = 'SELECT * FROM "user" WHERE name = ?';
        $rows = $db->query($sql, ['测试2']);
        var_dump($rows);
        self::assertIsArray($rows);

        //更新
        $sql = 'UPDATE "user" SET name = ? WHERE id = ?';
        $num = $db->query($sql, ['这是我想要写入的东西123！！',  7]);
        var_dump($num);
        self::assertEquals($num, 1);
    }

    public function testStartTrans()
    {
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');
        $db->startTrans();
        $db->commit();
        self::assertTrue(true);
    }

    public function testCommit()
    {
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');

        $db->startTrans();

        $sql = 'UPDATE "user" SET "name" = ? WHERE id = 6';
        $num = $db->query($sql, ["陈峰展1631"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->commit();

        $sql = 'SELECT * FROM "user" WHERE id = 6';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals($rows[0]['name'], '陈峰展1631');
    }

    public function testRollback()
    {
        $db = new Pdo('F:\data\sqlite3\gm_test.sqlite3');

        $db->startTrans();

        $sql = 'UPDATE "user" SET "name" = ? WHERE id = 6';
        $num = $db->query($sql, ["陈峰展16312"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->rollback();

        $sql = 'SELECT * FROM "user" WHERE id = 6';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals($rows[0]['name'], '陈峰展1631');
    }
}
