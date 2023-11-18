<?php

namespace Pkg6\DBALW\Builder;

use Doctrine\DBAL\Schema\Index;

/**
 * @property string indexName
 * @property Index index
 */
class IndexBuilder extends Builder
{
    /**
     * @var bool|mixed
     */
    protected $drop = true;
    /**
     * @var
     */
    protected $indexName;
    /**
     * @var Index|null
     */
    protected $index;

    /**
     * @param string $tableName
     * @param string $indexName
     * @param bool $drop
     * @param Index|null $index
     */
    public function __construct($tableName, $indexName, $drop = true, Index $index = null)
    {
        $this->drop      = $drop;
        $this->indexName = $indexName;
        $this->index     = $index;
        parent::__construct($tableName);
    }

    /**
     * @return string
     */
    public function toSQL()
    {
        if ($this->drop) {
            return "DROP INDEX `{$this->indexName}` ON `{$this->getTableName()}`";
        }
        $fields = implode(", ", $this->index->getColumns());
        return "CREATE INDEX {$this->indexName} ON {$this->getTableName()}({$fields})";
    }
}