<?php

namespace Tests\Extend\Access\Mode;

use Fize\Database\Extend\Access\Mode\ADODB;
use PHPUnit\Framework\TestCase;

class TestADODB extends TestCase
{

    public function test__construct()
    {
        $test_dir = dirname(__FILE__, 4);

        $file = $test_dir . '/data/test_with_password.mdb';
        $password = '123456';
        $db = new ADODB($file, $password);
        var_dump($db);
        self::assertIsObject($db);

        $file = $test_dir . '/data/test.mdb';
        $db = new ADODB($file);
        var_dump($db);
        self::assertIsObject($db);
    }

    public function test__destruct()
    {
        $test_dir = dirname(__FILE__, 4);
        $file = $test_dir . '/data/test_with_password.mdb';
        $password = '123456';
        $db = new ADODB($file, $password);
        var_dump($db);
        self::assertIsObject($db);
        unset($db);
        self::assertTrue(true);
    }

    public function testLastInsertId()
    {
        $test_dir = dirname(__FILE__, 4);
        $file = $test_dir . '/data/test_with_password.mdb';
        $password = '123456';
        $db = new ADODB($file, $password);
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
        $test_dir = dirname(__FILE__, 4);
        $file = $test_dir . '/data/test_with_password.mdb';
        $password = '123456';
        $db = new ADODB($file, $password);
        $prototype = $db->prototype();
        var_dump($prototype);
        self::assertIsObject($prototype);
    }

    public function testQuery()
    {
        $test_dir = dirname(__FILE__, 4);
        $file = $test_dir . '/data/test.mdb';
        $db = new ADODB($file);

        //增
        $sql = 'INSERT INTO [user] ([name],[add_time]) VALUES (?,?)';
        $num = $db->query($sql, ["!乱/七\八'糟\"的*字?符%串`一#大@堆(", time()]);
        var_dump($num);
        self::assertIsInt($num);

        //删
        $sql = 'DELETE FROM [user] WHERE id <= 7';
        $num = $db->query($sql);
        var_dump($num);
        self::assertIsInt($num);

        //改
        $sql = 'UPDATE [user] SET [name] = ? WHERE id = 17';
        $num = $db->query($sql, ["陈峰展"]);
        var_dump($num);
        self::assertIsInt($num);

        //查
        $sql = 'SELECT * FROM [user]';
        $rows = $db->query($sql);
        var_dump($rows);
        self::assertIsArray($rows);
    }

    public function testStartTrans()
    {
        $test_dir = dirname(__FILE__, 4);
        $file = $test_dir . '/data/test.mdb';
        $db = new ADODB($file);
        $db->startTrans();
        $db->commit();
        self::assertTrue(true);
    }

    public function testCommit()
    {
        $test_dir = dirname(__FILE__, 4);
        $file = $test_dir . '/data/test_with_password.mdb';
        $password = '123456';
        $db = new ADODB($file, $password);

        $db->startTrans();

        $sql = 'UPDATE [user] SET [name] = ? WHERE id = 6';
        $num = $db->query($sql, ["陈峰展1630"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->commit();

        $sql = 'SELECT * FROM [user] WHERE id = 6';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals($rows[0]['name'], '陈峰展1630');

    }

    public function testRollback()
    {
        $test_dir = dirname(__FILE__, 4);
        $file = $test_dir . '/data/test_with_password.mdb';
        $password = '123456';
        $db = new ADODB($file, $password);

        $db->startTrans();

        $sql = 'UPDATE [user] SET [name] = ? WHERE id = 6';
        $num = $db->query($sql, ["陈峰展1632"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->rollback();

        $sql = 'SELECT * FROM [user] WHERE id = 6';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals('陈峰展1630', $rows[0]['name']);
    }
}
