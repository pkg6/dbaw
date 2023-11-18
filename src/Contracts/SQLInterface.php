<?php

namespace Pkg6\DBALW\Contracts;

interface SQLInterface
{
    /**
     * @return string
     */
    public function toSQL();
}