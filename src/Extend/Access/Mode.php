<?php

namespace Fize\Database\Extend\Access;

use Fize\Database\Extend\Access\Mode\ADODB;
use Fize\Database\Extend\Access\Mode\ODBC;
use Fize\Database\Extend\Access\Mode\PDO;

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
     * @return ADODB
     */
    public static function adodb(string $file, string $pwd = null, string $driver = null): ADODB
    {
        return new ADODB($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     * @return ODBC
     */
    public static function odbc(string $file, string $pwd = null, string $driver = null): ODBC
    {
        return new ODBC($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     * @return PDO
     */
    public static function pdo(string $file, string $pwd = null, string $driver = null): PDO
    {
        return new PDO($file, $pwd, $driver);
    }
}
