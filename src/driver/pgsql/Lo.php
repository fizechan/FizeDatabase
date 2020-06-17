<?php

namespace fize\database\driver\pgsql;

/**
 * PostgreSQL大对象
 */
class Lo
{

    /**
     * @var resource 大型对象
     */
    protected $largeObject = null;

    /**
     * 构造
     * @param resource $large_object 大对象
     */
    public function __construct(&$large_object)
    {
        $this->largeObject = $large_object;
    }

    /**
     * 关闭一个大型对象
     * @return bool
     */
    public function loClose()
    {
        return pg_lo_close($this->largeObject);
    }

    /**
     * 读入整个大型对象并直接发送给浏览器
     * @return int 读入的字节数
     */
    public function loReadAll()
    {
        return pg_lo_read_all($this->largeObject);
    }

    /**
     * 从大型对象中读入数据
     * @param int $len 读入最多 len 字节的数据
     * @return string
     */
    public function loRead($len)
    {
        return pg_lo_read($this->largeObject, $len);
    }

    /**
     * 移动大型对象中的指针
     * @param int $offset 偏移量
     * @param int $whence 参数为 PGSQL_SEEK_SET，PGSQL_SEEK_CUR 或 PGSQL_SEEK_END
     * @return bool
     */
    public function loSeek($offset, $whence = 1)
    {
        return pg_lo_seek($this->largeObject, $offset, $whence);
    }

    /**
     * 返回大型对象的当前指针位置
     * @return int
     */
    public function loTell()
    {
        return pg_lo_tell($this->largeObject);
    }

    /**
     * 截断大对象
     * @param int $size 要截断的字节数
     * @return bool
     */
    public function loTruncate($size)
    {
        return pg_lo_truncate($this->largeObject, $size);
    }

    /**
     * 向大型对象写入数据
     * @param string $data 要写入的数据
     * @return int
     */
    public function loWrite($data)
    {
        return pg_lo_write($this->largeObject, $data);
    }
}
