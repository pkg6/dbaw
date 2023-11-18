<?php

namespace Pkg6\DBALW\Builder;


use Pkg6\DBALW\Abstracts\TableAbstract;
use Pkg6\DBALW\Contracts\SQLInterface;

abstract class Builder extends TableAbstract implements SQLInterface
{
    /**
     * 处理sql中value的值
     * @param $value
     * @return float|int|string
     */
    public static function SQLValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else if (is_null($value)) {
            return 'NULL';
        } else {
            return "'" . str_replace(["\r", "\n"], ['\\r', '\\n'], addslashes($value)) . "'";
        }
    }
}