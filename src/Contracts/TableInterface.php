<?php

namespace Pkg6\DBALW\Contracts;

interface TableInterface
{
    /**
     * @param string $tableName
     * @return $this
     */
    public function setTableName($tableName);

    /**
     * @return string
     */
    public function getTableName();
}