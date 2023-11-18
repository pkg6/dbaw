<?php

namespace Pkg6\DBALW\Concern;

use Doctrine\DBAL\Exception;
use Generator;

trait DBALYieldData
{
    /**
     * @return Generator
     * @throws Exception
     */
    public function yieldView()
    {
        $tables = $this->driver()->views();
        foreach ($tables as $table) {
            yield $table;
        }
    }

    /**
     * @return Generator
     * @throws Exception
     */
    public function yieldTable()
    {
        $tables = $this->driver()->tables();
        foreach ($tables as $table) {
            yield $table;
        }
    }

    /**
     * @param string $tableName
     * @param int $page
     * @param int $count
     * @param callable|null $queryBuilderCallable
     * @param string|string[]|null $select
     * @return \Generator|int
     * @throws Exception
     */
    public function yieldTableDataByPage(string $tableName, int $page = 1, int $count = 100, callable $queryBuilderCallable = null, $select = "*")
    {
        if ($page === 0) {
            return yield 0;
        }
        // 备份数据
        $offset = ($page - 1) * $count;
        $queryBuilder = $this->newQuery()
            ->select($select)
            ->from($tableName)
            ->setFirstResult($offset)
            ->setMaxResults($count);
        if (!is_null($queryBuilderCallable)) {
            $queryBuilderCallable($queryBuilder);
        }
        $resetSet = $queryBuilder->fetchAllAssociative();
        while (count($resetSet) > 0) {
            foreach ($resetSet as $data) {
                yield $data;
            }
            return yield $page + 1;
        }
        return yield 0;
    }
}