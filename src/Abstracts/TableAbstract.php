<?php

namespace Pkg6\DBALW\Abstracts;

use Pkg6\DBALW\Contracts\TableInterface;


abstract class TableAbstract implements TableInterface
{
    /**
     * @var string
     */
    protected $_tableName;

    /**
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->setTableName($tableName);
    }

    /**
     * @param string $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }
}