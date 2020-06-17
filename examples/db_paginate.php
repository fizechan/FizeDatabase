<?php
require_once "../vendor/autoload.php";

use fize\database\Db;

$config = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '123456',
    'dbname'   => 'gm_test'
];

new Db('mysql', $config);

//使用 paginate() 方法取得分页所需的所有参数

list($count, $page, $rows) = Db::table('user')->where(['sex' => 1])->field(['id', 'name'])->paginate(2, 3);
var_dump($count);
var_dump($page);
var_dump($rows);
var_dump(Db::getLastSql(true));
