<?php

namespace Pkg6\DBALW\Builder;

use Pkg6\DBALW\Support\Arr;

/**
 * @property array data
 */
class InsertBuilder extends Builder
{
    /**
     * @var string
     * 会向表中插入新记录。如果表中已存在与新记录主键或唯一索引冲突的记录,会报错并回滚。
     */
    public const METHODINSERTINTO = "INSERT INTO";
    /**
     * @var string
     * 会插入新记录。如果表中已存在主键或唯一索引冲突的记录,则忽略这条插入语句,不报错也不回滚。
     */
    public const METHODINSERTIGNOREINTO = "INSERT IGNORE INTO";

    /**
     * @var string
     * 会先删除表中已存在的与新记录主键或唯一索引冲突的记录,然后插入新记录。如果没有冲突记录则直接插入。
     */
    public const METHODREPLACEINTO = "REPLACE INTO";
    
    /**
     * @var string
     */
    protected $_method;
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param $tableName
     * @param $data
     */
    public function __construct($tableName, $data, $method = self::METHODINSERTINTO)
    {
        $this->data    = $data;
        $this->_method = $method;
        parent::__construct($tableName);
    }

    /**
     * @return $this
     */
    public function replaceInto()
    {
        $this->_method = self::METHODREPLACEINTO;
        return $this;
    }

    /**
     * @return $this
     */
    public function insertInto()
    {
        $this->_method = self::METHODINSERTINTO;
        return $this;
    }

    public function insertIgnoreInto()
    {
        $this->_method = self::METHODINSERTIGNOREINTO;
        return $this;
    }

    /**
     * @return string
     */
    public function toSQL()
    {
        $dimensions = Arr::getArrayDimensions($this->data);
        $columns    = [];
        $values     = [];
        switch ($dimensions) {
            case 1:
                foreach ($this->data as $column => $value) {
                    $columns[] = $column;
                    $values[]  = self::SQLValue($value);
                }
                break;
            case 2:
                $columns = array_keys(end($this->data));
                foreach ($this as $item) {
                    $rowValues = [];
                    foreach ($item as $value) {
                        $rowValues[] = self::SQLValue($value);
                    }
                    $values[] = "(" . implode(", ", $rowValues) . ")";
                }
                break;
            default:

        }
        if (empty($values)) {
            return "";
        }
        $sql = "{$this->_method} `{$this->getTableName()}` (";
        $sql .= implode(", ", $columns);
        $sql .= ") VALUES (";
        $sql .= implode(", ", $values);
        $sql .= ");";
        return $sql;
    }
}