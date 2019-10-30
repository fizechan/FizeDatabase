<?php


namespace fize\db\realization\mssql\mode;

use fize\db\realization\mssql\Db;

/**
 * Class Adodb
 * @todo 非推荐选择，暂不实现
 * @package fize\db\realization\mssql\mode
 */
class Adodb extends Db
{

    /**
     * @inheritDoc
     */
    public function prototype()
    {
        // TODO: Implement prototype() method.
    }

    /**
     * @inheritDoc
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        // TODO: Implement query() method.
    }

    /**
     * @inheritDoc
     */
    public function startTrans()
    {
        // TODO: Implement startTrans() method.
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * @inheritDoc
     */
    public function rollback()
    {
        // TODO: Implement rollback() method.
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId($name = null)
    {
        // TODO: Implement lastInsertId() method.
    }
}