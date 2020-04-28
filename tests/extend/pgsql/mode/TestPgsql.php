<?php

namespace extend\pgsql\mode;

use fize\db\extend\pgsql\mode\Pgsql;
use PHPUnit\Framework\TestCase;

class TestPgsql extends TestCase
{

    public function test__construct()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        var_dump($db);
        self::assertIsObject($db);
    }

    public function test__destruct()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        var_dump($db);
        unset($db);
        self::assertTrue(true);
    }

    public function testLastInsertId()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
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
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $prototype = $db->prototype();
        var_dump($prototype);
        self::assertIsObject($prototype);
    }

    public function testQuery()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");

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
        $num = $db->query($sql, ['这是我想要写入的东西123！！',  7]);
        var_dump($num);
        self::assertEquals($num, 1);
    }

    public function testStartTrans()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->startTrans();
        self::assertTrue(true);
    }

    public function testCommit()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->startTrans();
        $sql = 'UPDATE "user" SET name = ? WHERE id = ?';
        $num = $db->query($sql, ['这是我想要写入的东西123！！',  9]);
        var_dump($num);
        self::assertEquals($num, 1);
        $db->commit();
    }

    public function testRollback()
    {
        $db = new Pgsql("host=192.168.56.101 port=5432 dbname=gmtest user=root password=123456");
        $db->startTrans();
        $sql = 'UPDATE "user" SET name = ? WHERE id = ?';
        $db->query($sql, ['这是我想要写入的东西1234！！',  13]);
        $db->rollback();
        $sql = 'SELECT * FROM "user" WHERE id = 13';
        $rows = $db->query($sql);
        $row = $rows[0];
        var_dump($row);
        self::assertNotEquals($row['name'], '这是我想要写入的东西1234！！');
    }
}
