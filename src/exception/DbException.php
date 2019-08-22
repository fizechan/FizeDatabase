<?php

namespace fize\db\exception;

use Exception;

/**
 * 数据库错误
 */
class DbException extends Exception
{

    /**
     * @var string SQL语句
     */
    protected $sql;

    /**
     * DbException constructor.
     * @param string $message 错误信息
     * @param int $code 错误码
     * @param string $sql SQL语句
     */
    public function __construct($message = "", $code = 0, $sql = '')
    {
        parent::__construct($message, $code);
        $this->sql = $sql;
    }

    /**
     * 返回出错的相关SQL语句
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }
}