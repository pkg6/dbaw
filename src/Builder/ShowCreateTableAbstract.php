<?php

namespace Pkg6\DBALW\Builder;

use Pkg6\DBALW\Abstracts\TableAbstract;
use Pkg6\DBALW\Contracts\SQLInterface;

class ShowCreateTableAbstract extends TableAbstract implements SQLInterface
{
    public const TABLE = "Create Table";
    public const VIEW = "Create View";
    /**
     * @var string
     */
    protected $type = "";
    /**
     * @var mixed|string
     */
    protected $sql = "";

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $tableName = $data["Table"] ?? $data["View"];
        if (isset($data[ShowCreateTableAbstract::TABLE])) {
            $this->sql  = $data[ShowCreateTableAbstract::TABLE];
            $this->type = ShowCreateTableAbstract::TABLE;
        }
        if (isset($data[ShowCreateTableAbstract::VIEW])) {
            $this->sql  = $data[ShowCreateTableAbstract::VIEW];
            $this->type = ShowCreateTableAbstract::VIEW;
        }
        parent::__construct($tableName);
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @return mixed|string
     */
    public function toSQL()
    {
        return $this->sql;
    }
}