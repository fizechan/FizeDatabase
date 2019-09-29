<?php


use PHPUnit\Framework\TestCase;
use fize\db\Db;
use fize\db\Query;

class MssqlTest extends TestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $options = [
            'type'   => 'mysql',
            'mode'   => 'pdo',
            'config' => [
                'host'     => 'localhost',
                'user'     => 'root',
                'password' => '123456',
                'dbname'   => 'gm_test'
            ]
        ];

        Db::init($options);
    }

    public function testAdd()
    {
        $data = [
            'name'     => "!乱/七\八'糟\"的*字?符%串`一#大@堆(123",
            'add_time' => time()
        ];
        $rst = Db::table('user')->insert($data);
        var_dump($rst);
        echo "<br/>";
        $sql = Db::getLastSql(true);
        print_r($sql);
    }

    public function testAvg()
    {
        $user = Db::table('user');
        $avg = $user->where(['sex' => ['NEQ', 1]])->avg('sex');
        var_dump($avg);
        var_dump(Db::getLastSql(true));
    }

    public function testColumn()
    {
        $user = Db::table('user');
        $names = $user->where(['sex' => ['NEQ', 1]])->column('name');
        var_dump($names);
        var_dump(Db::getLastSql(true));
    }

    public function testCount()
    {
        $user = Db::table('user');
        $count = $user->where(['sex' => ['NEQ', 1]])->count('sex');
        var_dump($count);
        var_dump(Db::getLastSql(true));
    }

    public function testDelete()
    {
        $user = Db::table('user');
        $result = $user->where(['id' => 10])->delete();
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testLimit()
    {
        $user = Db::table('user');
        $rows = $user->where(['id' => ['>=', 4]])->order(['sex' => 'DESC'])->limit(3, 2)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
    }

    public function testMax()
    {
        $user = Db::table('user');
        $max = $user->where(['sex' => ['NEQ', 1]])->max('sex');
        var_dump($max);
        var_dump(Db::getLastSql(true));
    }

    public function testMin()
    {
        $user = Db::table('user');
        $min = $user->where(['sex' => ['NEQ', 1]])->min('sex');
        var_dump($min);
        var_dump(Db::getLastSql(true));
    }

    public function testPaginate()
    {
        $result = Db::table('user')->where(['sex' => ['NEQ', 1]])->order("sex ASC")->paginate(2, 3);
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testSelect()
    {
        $user = Db::table('user');
        $map2 = [
            'name' => '陈峰展2'
        ];
        $list = $user->where($map2)->limit(2)->select();
        echo $user->getLastSql();
        echo "<br/>";
        var_dump($list);
    }

    public function testSelectOr()
    {
        $user = Db::table('user');
        $map1 = [
            'name'     => "陈峰展'",
            'add_time' => ['BETWEEN', [1422720001, 1461226895]]
        ];

        $list1 = $user->where($map1)->select();
        var_dump($list1);
        echo "<br/>";
        echo $user->getLastSql(true);
        echo "<br/>";

        $map2 = [
            'name' => '35NEW,哈哈哈',
            'sex'  => ['=', 4, "OR"]
        ];
        $list2 = $user->where($map2)->select();
        var_dump($list2);
        echo "<br/>";
        echo $user->getLastSql();
    }

    public function testSum()
    {
        $user = Db::table('user');
        $sum = $user->where(['sex' => ['NEQ', 1]])->sum('sex');
        var_dump($sum);
        var_dump(Db::getLastSql(true));
    }

    public function testUpdate()
    {
        $user = Db::table('user');
        $data = [
            'name' => '梁燕萍',
            'sex'  => ['[sex] + 110']
        ];
        $result = $user->where(['id' => 75])->update($data);
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testValue()
    {
        $user = Db::table('user');
        $sex = $user->where(['id' => 23])->value('sex', 0, true);
        var_dump($sex);
        var_dump(Db::getLastSql(true));
    }
}
