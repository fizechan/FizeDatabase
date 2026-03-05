<?php

namespace Fize\Database\Extend\Access;

use Fize\Database\Extend\Access\Mode\ADODBMode;
use Fize\Database\Extend\Access\Mode\ODBCMode;
use Fize\Database\Extend\Access\Mode\PDOMode;

/**
 * 模式
 */
class Mode
{

    /**
     * odbc方式构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     * @return ADODBMode
     */
    public static function adodb(string $file, string $pwd = null, string $driver = null): ADODBMode
    {
        return new ADODBMode($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     * @return ODBCMode
     */
    public static function odbc(string $file, string $pwd = null, string $driver = null): ODBCMode
    {
        return new ODBCMode($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     * @return PDOMode
     */
    public static function pdo(string $file, string $pwd = null, string $driver = null): PDOMode
    {
        return new PDOMode($file, $pwd, $driver);
    }
}
