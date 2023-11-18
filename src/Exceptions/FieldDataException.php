<?php

namespace Pkg6\DBALW\Exceptions;

use InvalidArgumentException;

class FieldDataException extends InvalidArgumentException
{
    /**
     * @var string
     */
    protected $field;
    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $field
     * @param array $data
     */
    public function __construct($field, $data)
    {
        $this->field = $field;
        $this->data  = $data;
        parent::__construct("Missing {$field} field in data");
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}