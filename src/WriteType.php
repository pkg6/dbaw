<?php

namespace Pkg6\DBALW;

use Pkg6\DBALW\Contracts\WriteTypeInterface;

class WriteType implements WriteTypeInterface
{
    /**
     * @var int
     */
    protected $_type;

    /**
     * @param int $writeType
     */
    public function __construct(int $writeType)
    {
        $this->_type = $writeType;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }
}