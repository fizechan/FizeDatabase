<?php


use PHPUnit\Framework\TestCase;
use fize\db\Db;
use fize\db\Query;

class MysqlTest extends TestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $config = [
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => '123456',
            'dbname'   => 'gm_test2'
        ];

        new Db('mysql', $config, 'pdo');
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
        $result = $user->where(['id' => 73])->delete();
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testFetch()
    {
        $user = Db::table('user');

        $user->where(['sex' => ['NEQ', 1]])->fetch(
            function ($row) {
                var_dump($row);
            }
        );

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

    public function testOrder()
    {
        $result = Db::table('user')->order(['sex' => 'asc', 'add_time' => 'desc'])->select();
        var_dump($result);

        var_dump(Db::getLastSql(true));
    }

    public function testPaginate()
    {
        $result = Db::table('user')->where(['sex' => 1])->field(['id', 'name'])->paginate(2, 3);
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testSelect()
    {
        $user = Db::table('user');
        $map2 = [
            'name' => ['LIKE', '陈峰展%']
        ];
        $list = $user->where($map2)->limit(2)->select();
        echo $user->getLastSql();
        echo "<br/>";
        var_dump($list);
    }

    public function testSelectMulti()
    {
        $user = Db::table('user');

//var_dump($user);

//示例1，以数组连接
        $map1 = [
            'name'     => '35NEW,哈哈哈',
            'add_time' => ['<>', 1493712345, "OR"],  //EQ、OR不区分大小写
            "`name` IS NOT NULL"  //测试非标
        ];
        $map2 = [
            'add_time' => ['IN', "1493716872, 1493717205, 1493717205"]  //值为字符串格式，不含左右括号
        ];
        $map3 = [
            'name'     => '35NEW,哈哈哈',
            'add_time' => ['BETWEEN', 1493712345, 1493716872, "OR"]  //BETWEEN、OR不区分大小写
        ];
        $query1 = Query::qOr(Query::qAnd($map1, $map2), $map3);
        var_dump($query1);
        $list1 = $user->where($query1)->select();
        var_dump($list1);
        echo "<br/>";
        echo $user->getLastSql();
        echo "<br/>";

//示例2，以QueryMysql对象连接
        $map1 = Query::field('name')
            ->eq('35NEW,哈哈哈')
            ->logic('OR')
            ->field('add_time')
            ->neq(1493712345)
            ->logic('AND')
            ->field(null)
            ->exp('`name` IS NOT NULL');
        $map2 = Query::field('add_time')
            ->isIn("1493716872, 1493717205, 1493717205");
        $map3 = Query::object()
            ->field('name')
            ->eq('35NEW,哈哈哈')
            ->logic('OR')
            ->field('add_time')
            ->between(1493712345, 1493716872);
        $query2 = Query::qOr(Query::qAnd($map1, $map2), $map3);
        var_dump($query2);
        $list2 = $user->where($query2)->select();
        var_dump($list2);
        echo "<br/>";
        echo $user->getLastSql();
        echo "<br/>";
    }

    public function testSelectOr()
    {
        $user = Db::table('user');
        $map1 = [
            'name'     => "陈峰展'",
            'add_time' => [
                'between',
                [1422720001, 1461226895]
            ]
        ];

        $list1 = $user->where($map1)->select();
        var_dump($list1);
        echo "<br/>";
        echo $user->getLastSql(true);
        echo "<br/>";

        $map2 = array(
            'name' => '35NEW,哈哈哈',
            'sex'  => ['=', 4, "OR"]
        );
        $list2 = $user->where($map2)->select();
        var_dump($list2);
        echo "<br/>";
        echo $user->getLastSql();
    }

    public function testSetDec()
    {
        $user = Db::table('user');
        $result = $user->where(['id' => 75])->setDec('sex', 200);
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testSetInc()
    {
        $user = Db::table('user');
        $result = $user->where(['id' => 75])->setInc('sex');
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testSetValue()
    {
        $user = Db::table('user');
        $result = $user->where(['id' => 75])->setValue('sex', ['`sex` + 110']);
        var_dump($result);
        var_dump(Db::getLastSql(true));
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
            'sex'  => ['`sex` + 110']
        ];
        $result = $user->where(['id' => 75])->update($data);
        var_dump($result);
        var_dump(Db::getLastSql(true));
    }

    public function testValue()
    {
        $user = Db::table('user');
        $sex = $user->where(['id' => 75])->value('sex', 0, true);
        var_dump($sex);
        var_dump(Db::getLastSql(true));
    }
}
