<?php

namespace fize\database\extend\access;

use fize\database\extend\access\mode\Adodb;
use fize\database\extend\access\mode\Odbc;
use fize\database\extend\access\mode\Pdo;

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
     * @return Adodb
     */
    public static function adodb(string $file, string $pwd = null, string $driver = null): Adodb
    {
        return new Adodb($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc(string $file, string $pwd = null, string $driver = null): Odbc
    {
        return new Odbc($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string      $file   Access文件路径
     * @param string|null $pwd    用户密码
     * @param string|null $driver 指定ODBC驱动名称。
     * @return Pdo
     */
    public static function pdo(string $file, string $pwd = null, string $driver = null): Pdo
    {
        return new Pdo($file, $pwd, $driver);
    }
}
