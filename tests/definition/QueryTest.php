<?php

namespace definition;

use fize\db\Db;
use fize\db\definition\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $config = [
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => '123456',
            'dbname'   => 'gm_test'
        ];
        new Db('mysql', $config, 'pdo');
    }

    public function test__construct()
    {
        $query1 = new Query('name');
        $query1->eq('没毛病');
        $rows = Db::table('user')->where($query1)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);

        $query2 = new Query();
        $query2->exp("`sex`=917");
        $rows = Db::table('user')->where($query2)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testLogic()
    {
        $query = new Query();
        $query
            ->field('name')
            ->eq('没毛病')
            ->logic('OR')
            ->field('sex')
            ->eq(917);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testObject()
    {
        //@todo 该方法待移除，不测试
    }

    public function testField()
    {
        $query = new Query();
        $query
            ->field('name')
            ->eq('没毛病');
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testSql()
    {
        $query = new Query();
        $query
            ->field('name')
            ->eq('没毛病');
        $sql = $query->sql();
        var_dump($sql);
        self::assertEquals("name = '没毛病'", $sql);
    }

    public function testParams()
    {
        $query = new Query();
        $query
            ->field('name')
            ->like('=*&*^*');
        $sql = $query->sql();
        var_dump($sql);
        $params = $query->params();
        var_dump($params);
        self::assertNotEmpty($params);
    }

    public function testExp()
    {
        $query1 = new Query('sex');
        $query1->exp("> 1");
        $rows = Db::table('user')->where($query1)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);

        $query2 = new Query('name');
        $query2->exp("LIKE ?", '%陈峰展%');
        $rows = Db::table('user')->where($query2)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testCondition()
    {
        $query1 = new Query('sex');
        $query1->condition(">", 1);
        $rows = Db::table('user')->where($query1)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);

        $query2 = new Query('name');
        $query2->condition("LIKE", "?", '%陈峰展%');
        $rows = Db::table('user')->where($query2)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testGt()
    {
        $query = new Query('sex');
        $query->gt(2);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testEgt()
    {
        $query = new Query('sex');
        $query->egt(2);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testLt()
    {
        $query = new Query('sex');
        $query->lt(2);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testElt()
    {
        $query = new Query('sex');
        $query->elt(1);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testEq()
    {
        $query = new Query('sex');
        $query->eq(1);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testNeq()
    {
        $query = new Query('sex');
        $query->neq(1);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testBetween()
    {
        $query = new Query('sex');
        $query->between(2, 3);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testNotBetween()
    {
        $query = new Query('sex');
        $query->notBetween(2, 999);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testExists()
    {
        $query = new Query();
        $query->exists("SELECT 1 FROM `ms_user` WHERE `name` = `user`.name AND id > ?", 3);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testNotExists()
    {
        $query = new Query();
        $query->notExists("SELECT 1 FROM `ms_user` WHERE `name` = `user`.name AND id > 3");
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testIsIn()
    {
        $query1 = new Query('sex');
        $query1->isIn("1,2,3");
        $rows = Db::table('user')->where($query1)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);

        $query2 = new Query('sex');
        $query2->isIn([1, 2, 3]);
        $rows = Db::table('user')->where($query2)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testNotIn()
    {
        $query1 = new Query('sex');
        $query1->notIn("1,2,3");
        $rows = Db::table('user')->where($query1)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);

        $query2 = new Query('sex');
        $query2->notIn([1, 2, 3]);
        $rows = Db::table('user')->where($query2)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testLike()
    {
        $query = new Query('name');
        $query->like("%陈峰展%");
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testNotLike()
    {
        $query = new Query('name');
        $query->notLike("%陈峰展%");
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testIsNull()
    {
        $query = new Query('can_null');
        $query->isNull();
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testNotNull()
    {
        $query = new Query('can_null');
        $query->notNull();
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
        self::assertNotEmpty($rows);
    }

    public function testAnalyze()
    {
        /// 情况1：数组键名为字符串，键值为数组

        // NULL
        $query = new Query();
        $map = [
            'sex' => [null]
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex' => ['NULL']  //操作名NULL，不区分大小写
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex' => ['IS NULL']  //操作名IS NULL，不区分大小写(推荐写法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => [null, 'OR']  //附带连接逻辑
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex' => ['NOT NULL']  //操作名NOT NULL，不区分大小写
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex' => ['IS NOT NULL']  //操作名NOT NULL，不区分大小写(推荐写法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));


        // BETWEEN
        $query = new Query();
        $map = [
            'sex' => ['BETWEEN', [2, 3]]  //写法1(数组含2个元素, [1]为BETWEEN参数数组)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex' => ['BETWEEN', 2, 3]  //写法2情况1(数组含3个元素, [1]、[2]为BETWEEN参数)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['BETWEEN', [2, 3], 'OR']  //写法2情况2(数组含2个元素, [1]为BETWEEN参数数组, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['BETWEEN', 2, 3, 'OR']  //写法3(数组含4个元素, [1]、[2]为BETWEEN参数, [3]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['NOT BETWEEN', 2, 3, 'OR']  //NOT BETWEEN 的参数形式和 BETWEEN 的参数同理
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // CONDITION
        $query = new Query();
        $map = [
            'sex' => ['CONDITION', '<=', 2]  //写法1(数组含3个元素, [1]为判断符, [2]为值)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex' => ['CONDITION', 'IN', '(2,?)', 3]  //写法2(数组含4个元素, [1]为判断符, [2]为值, [3]为绑定参数)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['CONDITION', 'IN', '(2,?)', 3, 'OR']  //写法3(数组含5个元素, [1]为判断符, [2]为值, [3]为绑定参数, [4]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 大于等于
        $query = new Query();
        $map = [
            'sex' => ['>=', 3]  //写法1(数组含2个元素, [0]可以为">="或者"EGT",不需区分大小写, [1]为值)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['EGT', 3, 'OR']  //写法2(数组含3个元素, [0]可以为">="或者"EGT",不需区分大小写, [1]为值, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 小于等于
        $query = new Query();
        $map = [
            'sex' => ['<=', 3]  //写法1(数组含2个元素, [0]可以为"<="或者"ELT",不需区分大小写, [1]为值)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['ELT', 3, 'OR']  //写法2(数组含3个元素, [0]可以为">="或者"ELT",不需区分大小写, [1]为值, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 等于
        $query = new Query();
        $map = [
            'sex' => ['=', 3]  //写法1(数组含2个元素, [0]可以为"="或者"EQ",不需区分大小写, [1]为值)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['EQ', 3, 'OR']  //写法2(数组含3个元素, [0]可以为"="或者"EQ",不需区分大小写, [1]为值, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 原始表达式语句
        $query = new Query();
        $map = [
            'name' => ['EXP', "='没毛病'"],  // 写法1(数组含2个元素， [1]为SQL语法，参考exp方法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => ['EXP', "LIKE ?", '陈峰展'],  // 写法2(数组含2个元素， [1]为SQL语法, [2]为绑定参数，参考exp方法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex'  => 3,
            'name' => ['EXP', "LIKE ?", '陈峰展', 'OR'],  // 写法3(数组含2个元素， [1]为SQL语法, [2]为绑定参数，参考exp方法, [3]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 大于
        $query = new Query();
        $map = [
            'sex' => ['>', 3]  //写法1(数组含2个元素, [0]可以为">="或者"GT",不需区分大小写, [1]为值)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['GT', 3, 'OR']  //写法2(数组含3个元素, [0]可以为">"或者"GT",不需区分大小写, [1]为值, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // IN
        $query = new Query();
        $map = [
            'sex' => ['IN', '2,3']  //写法1(数组含2个元素, [1]为值(参考isIn方法参数))
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['IN', [2, 3], 'OR']  //写法1(数组含2个元素, [1]为值(参考isIn方法参数), [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['NOT IN', [2, 3], 'OR']  //NOT IN 和 IN 的参数同理
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // LIKE
        $query = new Query();
        $map = [
            'name' => ['LIKE', '%陈峰展%']  //写法1(数组含2个元素, [1]为值(参考like方法参数))
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex'  => '3',
            'name' => ['LIKE', '%陈峰展%', 'OR']  //写法1(数组含2个元素, [1]为值(参考like方法参数), [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex'  => '3',
            'name' => ['NOT LIKE', '%陈峰展%', 'OR']  //NOT LIKE 的参数和 LIKE 的参数同理
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 小于
        $query = new Query();
        $map = [
            'sex' => ['<', 3]  //写法1(数组含2个元素, [0]可以为"<"或者"LT",不需区分大小写, [1]为值)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['LT', 3, 'OR']  //写法2(数组含3个元素, [0]可以为"<"或者"LT",不需区分大小写, [1]为值, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 不等于
        $query = new Query();
        $map = [
            'sex' => ['!=', 3]  //写法1(数组含2个元素, [0]可以为"!="或者"<>"或者"NEQ",不需区分大小写, [1]为值)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => '没毛病',
            'sex'  => ['NEQ', 3, 'OR']  //写法2(数组含3个元素, [0]可以为"!="或者"<>"或者"NEQ",不需区分大小写, [1]为值, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 默认情况，其实是EXP情况的变种
        $query = new Query();
        $map = [
            'name' => ["='没毛病'"],  // 写法1(数组含1个元素， [0]为SQL语法，参考exp方法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'name' => ["LIKE ?", '陈峰展'],  // 写法2(数组含2个元素， [0]为SQL语法, [1]为绑定参数，参考exp方法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex'  => 3,
            'name' => ["LIKE ?", '陈峰展', 'OR'],  // 写法3(数组含3个元素， [0]为SQL语法, [1]为绑定参数，参考exp方法, [2]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        /// 情况2：数组键名为字符串，键值为 null(效果等同于IS NULL)
        $query = new Query();
        $map = [
            'sex' => null
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        self::assertTrue(true);

        /// 情况3：数组键名为字符串，键值为标量(效果等同于EQ)
        $query = new Query();
        $map = [
            'sex'  => 1,
            'name' => '没毛病',
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        /// 情况4：数组没有显式指定键名，或者键名为无意义的非字符串型，键值为数组

        // EXISTS
        $query = new Query();
        $map = [
            ['EXISTS', 'SELECT 1 FROM `ms_user` WHERE `name` = `user`.name AND id > 1']  // 写法1(数组含2个元素， [1]为SQL语法, 参考exists方法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            ['EXISTS', 'SELECT 1 FROM `ms_user` WHERE `name` = `user`.name AND id > ?', 2]  // 写法2(数组含3个元素， [1]为SQL语法, [2]为绑定参数, 参考exists方法)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex'  => 1,
            ['EXISTS', 'SELECT 1 FROM `ms_user` WHERE `name` = `user`.name AND id > ?', 2, 'OR']  // 写法3(数组含3个元素， [1]为SQL语法, [2]为绑定参数, 参考exists方法, [3]为连接逻辑)
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        $query = new Query();
        $map = [
            'sex'  => 1,
            ['NOT EXISTS', 'SELECT 1 FROM `ms_user` WHERE `name` = `user`.name AND id > ?', 2, 'OR']  // NOT EXISTS 和 EXISTS 的参数同理
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        // 其余情况：首个元素作为字段名(即EXISTS 、NOT EXISTS作为关键字，不应作为字段名)，其余字段作为参数，使用情况1的逻辑。
        $query = new Query();
        $map = [
            'sex'  => 3,
            ['name', "LIKE ?", '陈峰展', 'OR'],
            ['name', "LIKE ?", '梁燕萍', 'OR'],  //使用此方式可以同时对同个字段进行多次定义
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));

        /// 情况5：数组没有显式指定键名，或者键名为无意义的非字符串型，键值为字符串
        $query = new Query();
        $map = [
            'sex'  => 3,
            "name LIKE '%陈峰展%'",  // 此时该键值作为原始SQL表达式
            "name LIKE '%梁燕萍%'",  //使用此方式可以同时对同个字段进行多次定义
        ];
        $query->analyze($map);
        $rows = Db::table('user')->where($query)->select();
        var_dump($rows);
        var_dump(Db::getLastSql(true));
    }

    public function testQMerge()
    {
        //@todo 该方法待转移，不测试
    }

    public function testQAnd()
    {
        //@todo 该方法待转移，不测试
    }

    public function testQOr()
    {
        //@todo 该方法待转移，不测试
    }
}
