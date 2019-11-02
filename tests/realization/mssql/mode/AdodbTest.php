<?php

namespace realization\mssql\mode;

use fize\db\realization\mssql\mode\Adodb;
use PHPUnit\Framework\TestCase;

class AdodbTest extends TestCase
{

    public function test__construct()
    {
        $host = '(local)';
        $user = 'sa';
        $password = '123456';
        $dbname = 'gm_test';
        $db = new Adodb($host, $user, $password, $dbname);
        var_dump($db);
        self::assertIsObject($db);
    }

    public function test__destruct()
    {
        $db = new Adodb('(local)', 'sa', '123456', 'gm_test');
        var_dump($db);
        self::assertIsObject($db);
        unset($db);
        self::assertTrue(true);
    }

    public function testLastInsertId()
    {
        $db = new Adodb('(local)', 'sa', '123456', 'gm_test');
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
        $db = new Adodb('(local)', 'sa', '123456', 'gm_test');
        $prototype = $db->prototype();
        var_dump($prototype);
        self::assertIsObject($prototype);
    }

    public function testQuery()
    {
        $db = new Adodb('(local)', 'sa', '123456', 'gm_test');

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
        $db = new Adodb('(local)', 'sa', '123456', 'gm_test');
        $db->startTrans();
        $db->commit();
        self::assertTrue(true);
    }

    public function testCommit()
    {
        $db = new Adodb('(local)', 'sa', '123456', 'gm_test');

        $db->startTrans();

        $sql = 'UPDATE [user] SET [name] = ? WHERE id = 8';
        $num = $db->query($sql, ["陈峰展1630"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->commit();

        $sql = 'SELECT * FROM [user] WHERE id = 8';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals($rows[0]['name'], '陈峰展1630');

    }

    public function testRollback()
    {
        $db = new Adodb('(local)', 'sa', '123456', 'gm_test');

        $db->startTrans();

        $sql = 'UPDATE [user] SET [name] = ? WHERE id = 8';
        $num = $db->query($sql, ["陈峰展1632"]);
        var_dump($num);
        self::assertIsInt($num);

        $db->rollback();

        $sql = 'SELECT * FROM [user] WHERE id = 8';
        $rows = $db->query($sql);
        var_dump($rows[0]);
        self::assertEquals($rows[0]['name'], '陈峰展1630');
    }
}
