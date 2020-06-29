<?php

namespace fize\database\extend\mssql\mode;

use PDO as SysPDO;
use fize\database\extend\mssql\Db;
use fize\database\middleware\Pdo as Middleware;

/**
 * PDO
 *
 * PDO模式MSSQL数据库模型类
 * php_pdo_sqlsrv.dll需要本地客户端支持，不同版本使用的客户端不同，可以在错误信息中获取相关资料。
 * php_pdo_sqlsrv.dll由微软官方提供技术支持，推荐使用。
 * @see https://docs.microsoft.com/en-us/sql/connect/php/system-requirements-for-the-php-sql-driver
 * @see https://www.microsoft.com/en-us/download/details.aspx?id=55642
 */
class Pdo extends Db
{
    use Middleware;

    /**
     * 构造
     * @param string $host    服务器地址
     * @param string $user    用户名
     * @param string $pwd     用户密码
     * @param string $dbname  数据库名
     * @param mixed  $port    端口号，选填，MSSQL默认是1433
     * @param string $charset 指定数据库编码，选填，默认GBK
     * @param array  $opts    PDO连接的其他选项，选填
     */
    public function __construct($host, $user, $pwd, $dbname, $port = "", $charset = "GBK", array $opts = [])
    {
        $charset = strtoupper($charset);
        $charset_map = [
            'UTF8' => 'UTF-8',
        ];
        $charset = isset($charset_map[$charset]) ? $charset_map[$charset] : $charset;
        $dsn = "sqlsrv:Server={$host}";
        if (!empty($port)) {
            $dsn .= ",{$port}";
        }
        $dsn .= ";Database={$dbname}";
        if ($charset != "UTF-8") {
            $opts = $opts + [
                    SysPDO::ATTR_CASE              => SysPDO::CASE_LOWER,
                    SysPDO::ATTR_ERRMODE           => SysPDO::ERRMODE_EXCEPTION,
                    SysPDO::ATTR_STRINGIFY_FETCHES => false,
                    SysPDO::SQLSRV_ATTR_ENCODING   => SysPDO::SQLSRV_ENCODING_UTF8,
                ];
        } else {
            $opts = $opts + [
                    SysPDO::ATTR_CASE              => SysPDO::CASE_LOWER,
                    SysPDO::ATTR_ERRMODE           => SysPDO::ERRMODE_EXCEPTION,
                    SysPDO::ATTR_STRINGIFY_FETCHES => false,
                    SysPDO::SQLSRV_ATTR_ENCODING   => SysPDO::SQLSRV_ENCODING_DEFAULT,
                ];
        }
        $this->pdoConstruct($dsn, $user, $pwd, $opts);
    }

    /**
     * 析构时释放PDO资源
     */
    public function __destruct()
    {
        $this->pdoDestruct();
        parent::__destruct();
    }
}
