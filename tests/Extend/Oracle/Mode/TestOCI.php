<?php

namespace Tests\Extend\Oracle\Mode;

use Fize\Database\extend\Oracle\mode\OCI;
use PHPUnit\Framework\TestCase;

class TestOCI extends TestCase
{

    public function test__construct()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        self::assertIsObject($oci);
    }

    public function test__destruct()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        var_dump($oci);
        $oci = null;
        self::assertNull($oci);
    }

    public function testPrototype()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $prototype = $oci->prototype();
        self::assertIsObject($prototype);
    }

    public function testQuery()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');

        //插入
        $sql = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $result = $oci->query($sql, ['text' => '这是我想要写入的东西！！']);
        var_dump($result);
        self::assertEquals($result, 0);

        //查询
        $sql = "SELECT * FROM BIND_EXAMPLE_2 WHERE ISNULL = :isnull";
        $rows = $oci->query($sql, ['isnull' => 1]);
        var_dump($rows);
        self::assertIsArray($rows);

        //更新
        $sql = "UPDATE BIND_EXAMPLE_2 SET NAME = :name WHERE ISNULL = :isnull";
        $num = $oci->query($sql, ['name' => '这是我想要写入的东西123！！', 'isnull' => 1]);
        var_dump($num);
        self::assertEquals($num, 1);
    }

    public function testStartTrans()
    {
        $oci = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $oci->startTrans();
        self::assertTrue(true);
    }

    public function testCommit()
    {
        $oci1 = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $oci1->startTrans();
        $sql = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $result = $oci1->query($sql, ['text' => '这是我想要写入的东西456？？']);
        var_dump($result);
        self::assertEquals($result, 0);
        $oci1->rollback();
        //未提交时该语句不生效

        $oci2 = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $oci2->startTrans();
        $sql = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $result = $oci2->query($sql, ['text' => '这是我想要写入的东西789？？']);
        var_dump($result);
        self::assertEquals($result, 0);
        $oci2->commit();
        //显式提交生效

        self::assertTrue(true);
    }

    public function testRollback()
    {
        $oci1 = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $oci1->startTrans();
        $sql = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $result = $oci1->query($sql, ['text' => '这是我想要写入的东西456？？']);
        var_dump($result);
        self::assertEquals($result, 0);
        $oci1->rollback();
        //未提交时该语句不生效

        $oci2 = new OCI("OT", "Orcl123456", "127.0.0.1/gmtest", 'UTF8');
        $oci2->startTrans();
        $sql = "INSERT INTO BIND_EXAMPLE VALUES(:text)";
        $result = $oci2->query($sql, ['text' => '这是我想要写入的东西789？？']);
        var_dump($result);
        self::assertEquals($result, 0);
        $oci2->commit();
        //显式提交生效

        self::assertTrue(true);
    }
}
