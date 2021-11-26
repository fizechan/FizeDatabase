<?php

namespace Tests\Extend\MySQL;

use Fize\Database\Extend\MySQL\ModeFactory;
use PHPUnit\Framework\TestCase;

class TestModeFactory extends TestCase
{

    public function testCreate()
    {
        $config = [
            'host'     => '127.0.0.1',
            'user'     => 'root',
            'password' => '123456',
            'dbname'   => 'gm_test'
        ];
        $db = ModeFactory::create('pdo', $config);
        var_dump($db);
        self::assertIsObject($db);
    }
}
