<?php
require_once "../vendor/autoload.php";

use fize\db\Db;

//设置默认连接
$config = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '123456',
    'dbname'   => 'gm_test'
];

new Db('mysql', $config, 'pdo');

$rows = Db::table('user')
    ->where([
        'name' => ['LIKE', '陈峰展%']
    ])
    ->limit(2)
    ->select();
var_dump($rows);

//设置新连接
$config = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '123456',
    'dbname'   => 'gm_test2',
    'prefix'   => 'gm_'
];
$db = Db::connect('mysql', $config);

$rows = $db
    ->table('admin')
    ->limit(10)
    ->select();
var_dump($rows);
