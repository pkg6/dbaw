<?php

namespace Pkg6\DBALW\Contracts;

interface WriteRuleInterfce
{
    /**
     * 返回true表示忽略此数据，否则就进行执行写入
     * @param WriteInterface $write
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return bool
     */
    public function rule(WriteInterface $write, $data, WriteTypeInterface $writeType);
}