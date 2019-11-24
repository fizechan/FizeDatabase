<?php
require_once "../vendor/autoload.php";

use fize\db\Db;

$config = [
    'type'   => 'mysql',
    'mode'   => 'pdo',
    'config' => [
        'host'     => 'localhost',
        'user'     => 'root',
        'password' => '123456',
        'dbname'   => 'gm_test'
    ]
];

new Db($config);

//使用 paginate() 方法取得分页所需的所有参数

list($count, $page, $rows) = Db::table('user')->where(['sex' => 1])->field(['id', 'name'])->paginate(2, 3);
var_dump($count);
var_dump($page);
var_dump($rows);
var_dump(Db::getLastSql(true));