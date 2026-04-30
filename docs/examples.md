# 使用示例

本章节提供从基础 CRUD 到复杂查询、事务处理与性能优化的完整使用示例集合。

## 基础连接与查询

### 创建连接

```php
<?php
require_once "vendor/autoload.php";

use Fize\Database\Db;

// MySQL PDO 连接
$config = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '123456',
    'dbname'   => 'test_db',
    'port'     => 3306,
    'charset'  => 'utf8mb4'
];
new Db('mysql', $config);

// 指定模式
new Db('mysql', $config, 'pdo');    // PDO 模式
new Db('mysql', $config, 'mysqli'); // MySQLi 模式
```

### 条件查询

```php
// 基本查询
$users = Db::table('users')->select();

// 条件查询
$users = Db::table('users')
    ->where(['status' => 1])
    ->limit(10)
    ->select();

// 查看最终 SQL
$sql = Db::getLastSql(true);
```

## 插入与自增

```php
// 单条插入
$insertData = [
    'name'  => '张三',
    'email' => 'zhangsan@example.com'
];
Db::table('users')->insert($insertData);

// 插入并获取自增 ID
$id = Db::table('users')->insertGetId($insertData);

// 查看最终 SQL
$sql = Db::getLastSql(true);
```

## 更新与原值写入

```php
// 普通更新
Db::table('users')
    ->where(['id' => 1])
    ->update(['name' => '王五']);

// 原样 SQL 表达式（自增）
Db::table('users')
    ->where(['id' => 1])
    ->update(['login_count' => ['login_count + 1']]);
```

## 删除操作

```php
Db::table('users')
    ->where(['id' => 1])
    ->delete();
```

## 复杂查询与分页

```php
// 多条件查询
$users = Db::table('users')
    ->field(['id', 'name', 'email'])
    ->where(['status' => 1])
    ->where(['age' => ['>', 18]])
    ->order('id DESC')
    ->limit(20)
    ->select();

// MySQL 完整分页
list($total, $rows, $pages) = Db::table('users')
    ->where(['status' => 1])
    ->paginate(1, 20);
```

## 事务处理

```php
use Fize\Database\Db;

Db::startTrans();
try {
    // 扣减库存
    Db::table('products')
        ->where(['id' => 1])
        ->update(['stock' => ['stock - 1']]);

    // 插入订单
    Db::table('orders')->insert([
        'product_id' => 1,
        'quantity'   => 1,
        'status'     => 'pending'
    ]);

    Db::commit();
} catch (\Exception $e) {
    Db::rollback();
    throw $e;
}
```

### 嵌套事务

startTrans/commit/rollback 支持嵌套计数，仅在最外层生效：

```php
Db::startTrans();     // 层级 = 1，真正开启事务
try {
    Db::startTrans(); // 层级 = 2，仅增加计数

    // 业务操作...

    Db::commit();     // 层级 = 1，仅减少计数
    Db::commit();     // 层级 = 0，真正提交
} catch (\Exception $e) {
    Db::rollback();   // 层级归零并回滚
}
```

## 不同数据库类型的对比

### MySQL

```php
// MySQL 特有功能
Db::table('users')->lock(true)->select();                    // 锁表
Db::table('users')->replace(['id' => 1, 'name' => '张三']); // REPLACE
Db::table('users')->truncate();                               // TRUNCATE
Db::table('users')->insertAll([...]);                        // 批量插入
```

### Access

```php
// Access 使用 TOP 模拟分页
$config = [
    'file'   => 'C:/data/test.mdb',
    'driver' => 'Microsoft Access Driver (*.mdb, *.accdb)'
];
new Db('access', $config, 'adodb');

// 注意值转义策略不同
```

### SQLite

```php
$config = [
    'file' => '/path/to/database.db'
];
new Db('sqlite', $config, 'pdo');

// SQLite 支持 REPLACE、TRUNCATE、LIMIT
Db::table('users')->replace(['id' => 1, 'name' => '张三']);
```

## 查询器与复杂条件

### 使用 Query 对象

```php
use Fize\Database\Query;

// 创建查询器
$query = new Query('mysql');
$query->field('status')->eq(1);

// AND 组合
$query1 = (new Query('mysql'))->field('age')->gt(18);
$query2 = (new Query('mysql'))->field('status')->eq(1);
$combined = Query::qAnd($query1, $query2);

// 在 where 中使用
$users = Db::table('users')->where($combined)->select();
```

### 数组条件

```php
// 简单条件
Db::table('users')->where(['name' => '张三'])->select();

// 复合条件
Db::table('users')->where([
    'name'  => '张三',
    'age'   => ['>', 18],
    'status' => ['IN', [1, 2, 3]]
])->select();
```

## 性能优化实践

```php
// 关闭缓存
$result = Db::table('users')->select(false);

// 使用 fetch 回调减少内存
Db::table('users')->fetch(function($row) {
    // 逐行处理
    echo $row['name'] . "\n";
});

// 指定字段，避免 SELECT *
Db::table('users')->field(['id', 'name'])->select();

// 使用 limit/page 控制返回规模
Db::table('users')->limit(100)->select();

// 审核最终 SQL
$sql = Db::getLastSql(true);
```

## 常见业务场景

### 用户管理

```php
// 带分页的用户列表
list($total, $users, $pages) = Db::table('users')
    ->field(['id', 'name', 'email', 'created_at'])
    ->where(['status' => 1])
    ->order('id DESC')
    ->paginate(1, 20);
```

### 商品查询

```php
// 多条件商品搜索
$products = Db::table('products')
    ->where(['category_id' => ['IN', [1, 2, 3]]])
    ->where(['price' => ['BETWEEN', [100, 500]]])
    ->where(['name' => ['LIKE', '%手机%']])
    ->select();
```

### 订单处理

```php
// 使用事务包裹多步写入
Db::startTrans();
try {
    Db::table('products')->where(['id' => $productId])->update(['stock' => ['stock - 1']]);
    Db::table('orders')->insert(['product_id' => $productId, 'quantity' => 1]);
    Db::commit();
} catch (\Exception $e) {
    Db::rollback();
    throw $e;
}
```