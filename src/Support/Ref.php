<?php

namespace Pkg6\DBALW\Support;

use ReflectionClass;

class Ref
{
    /**
     * @param object $obj
     * @return array
     */
    public static function publicVar(object $obj)
    {
        return get_object_vars($obj);
    }
}