<?php

namespace Pkg6\DBALW\Contracts;

interface WriteTypeInterface
{
    //代码注释
    public const ANNOTATION = 1;
    //表结构
    public const TABLESTRUCTURE = 2;
    //表数据
    public const TABLEDATA = 3;

    /**
     * @return int
     */
    public function getType();
}