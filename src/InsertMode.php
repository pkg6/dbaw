<?php

namespace Pkg6\DBALW;

use Pkg6\DBALW\Builder\InsertBuilder;
use Pkg6\DBALW\Builder\SQLBuilder;
use Pkg6\DBALW\Contracts\InsertModeInterface;

class InsertMode implements InsertModeInterface
{

    /**
     * @var string
     * @see InsertBuilder::METHODINSERTINTO
     * @see InsertBuilder::METHODINSERTIGNOREINTO
     * @see InsertBuilder::METHODREPLACEINTO
     */
    protected $_method = InsertBuilder::METHODINSERTINTO;

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * @return int
     */
    public function method()
    {
        if ($this->_method == "") {
            $this->_method = InsertBuilder::METHODINSERTINTO;
        }
        return $this->_method;
    }

    /**
     * @param $tableName
     * @param $data
     * @return string
     */
    public function dataSQL($tableName, $data)
    {
        return SQLBuilder::InsertBuilder($tableName, $data, $this->method())->toSQL();
    }
}