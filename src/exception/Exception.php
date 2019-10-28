<?php

namespace fize\db\exception;

use Exception as BaseException;

/**
 * 数据库错误
 */
class Exception extends BaseException
{

    /**
     * @var string SQL语句
     */
    protected $sql;

    /**
     * Exception constructor.
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