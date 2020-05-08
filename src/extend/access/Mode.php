<?php

namespace fize\db\extend\access;

use fize\db\extend\access\mode\Adodb;
use fize\db\extend\access\mode\Odbc;
use fize\db\extend\access\mode\Pdo;

/**
 * 模式
 */
class Mode
{

    /**
     * odbc方式构造
     * @param string $file   Access文件路径
     * @param string $pwd    用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Adodb
     */
    public static function adodb($file, $pwd = null, $driver = null)
    {
        return new Adodb($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file   Access文件路径
     * @param string $pwd    用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Odbc
     */
    public static function odbc($file, $pwd = null, $driver = null)
    {
        return new Odbc($file, $pwd, $driver);
    }

    /**
     * odbc方式构造
     * @param string $file   Access文件路径
     * @param string $pwd    用户密码
     * @param string $driver 指定ODBC驱动名称。
     * @return Pdo
     */
    public static function pdo($file, $pwd = null, $driver = null)
    {
        return new Pdo($file, $pwd, $driver);
    }
}
