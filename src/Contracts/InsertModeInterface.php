<?php

namespace Pkg6\DBALW\Contracts;


interface InsertModeInterface
{

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method);

    /**
     * @param string $tableName
     * @param array $data
     * @return string
     */
    public function dataSQL($tableName, $data);
}