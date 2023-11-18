<?php

namespace Pkg6\DBALW\Rule;

use Pkg6\DBALW\Contracts\WriteInterface;
use Pkg6\DBALW\Contracts\WriteRuleInterfce;
use Pkg6\DBALW\Contracts\WriteTypeInterface;

class WriterIgnoreTable implements WriteRuleInterfce
{
    /**
     * @var array
     */
    protected $ignoreTable = [];

    /**
     * @param array $ignoreTable
     */
    public function __construct($ignoreTable)
    {
        $this->ignoreTable = $ignoreTable;
    }

    /**
     * 返回true表示忽略ignoreTable中的表，否则就进行执行写入
     * @param WriteInterface $write
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return bool
     */
    public function rule(WriteInterface $write, $data, WriteTypeInterface $writeType)
    {
        return in_array($write->getTableName(), $this->ignoreTable);
    }
}