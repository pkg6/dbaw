<?php

namespace Pkg6\DBALW\Builder;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Column;

/**
 * @property string fieldName
 * @property Column column
 * @property AbstractPlatform platform
 */
class FieldBuilder extends Builder
{
    /**
     * @var bool|mixed
     */
    protected $drop = true;
    /**
     * @var
     */
    protected $fieldName;
    /**
     * @var Column|null
     */
    protected $column;

    /**
     * @var AbstractPlatform|null
     */
    protected $platform;

    /**
     * @param string $tableName
     * @param string $fieldName
     * @param bool $drop
     * @param Column|null $column
     * @param AbstractPlatform|null $platform
     */
    public function __construct($tableName, $fieldName, $drop = true, Column $column = null, AbstractPlatform $platform = null)
    {
        $this->drop      = $drop;
        $this->fieldName = $fieldName;
        $this->column    = $column;
        $this->platform  = $platform;
        parent::__construct($tableName);
    }

    /**
     * @return string
     */
    public function toSQL()
    {
        if ($this->drop) {
            return "ALTER TABLE `{$this->tableName}` DROP COLUMN `{$this->getTableName()}`";
        }
        $declaration = $this->column->getType()->getSQLDeclaration([$this->fieldName], $this->platform);
        $sql         = "ALTER TABLE `{$this->getTableName()}` ADD COLUMN {$this->fieldName} {$declaration}";
        if ($this->column->getDefault() !== null) {
            $sql .= " DEFAULT " . $this->column->getDefault();
        }
        if ($this->column->getComment()) {
            $sql .= " COMMENT '" . $this->column->getComment() . "'";
        }
        return $sql;

    }
}