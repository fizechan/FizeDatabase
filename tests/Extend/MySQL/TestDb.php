<?php

namespace Tests\Extend\MySQL;

use Fize\Database\Extend\MySQL\ModeFactory;
use PHPUnit\Framework\TestCase;

class TestDb extends TestCase
{

    public function testPaginate()
    {
        $config = [
            'host'     => '127.0.0.1',
            'user'     => 'root',
            'password' => '123456',
            'dbname'   => 'gm_test'
        ];
        $db = ModeFactory::create('pdo', $config);
        $result = $db->table('auth_rule', 'fz_')->paginate(1, 5);
        var_dump($result);
        self::assertIsArray($result);
    }

    public function testInsertAll()
    {

    }

    public function testCrossJoin()
    {

    }

    public function testLeftOuterJoin()
    {

    }

    public function testTruncate()
    {

    }

    public function testReplace()
    {

    }

    public function testStraightJoin()
    {

    }

    public function testLock()
    {

    }

    public function testLimit()
    {

    }

    public function testRightOuterJoin()
    {

    }
}
