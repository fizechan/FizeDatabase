<?php
require_once "../vendor/autoload.php";

use fize\db\Db;

$config = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '123456',
    'dbname'   => 'gm_test'
];

new Db('mysql', $config);

$data = [
    'name' => '梁燕萍',
    'sex'  => ['`sex` + 110']  //原样SQL语句
];
$result = Db::table('user')->where(['id' => 75])->update($data);
var_dump($result);
var_dump(Db::getLastSql(true));
