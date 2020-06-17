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

$result = Db::table('user')->where(['id' => 73])->delete();
var_dump($result);
var_dump(Db::getLastSql(true));
