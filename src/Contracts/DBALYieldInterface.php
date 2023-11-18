<?php

namespace Pkg6\DBALW\Contracts;

use Doctrine\DBAL\Exception;

interface DBALYieldInterface
{
    /**
     * @return mixed
     */
    public function yieldTable();

    /**
     *
     * @return \Generator
     * @throws Exception
     */
    public function yieldView();

    /**
     * 获取表数据
     * @param string $tableName
     * @param int $page
     * @param int $count
     * @param callable|null $queryBuilderCallable
     * @return \Generator|int
     * @throws Exception
     */
    public function yieldTableDataByPage(string $tableName, int $page = 1, int $count = 100,callable $queryBuilderCallable=null);

}