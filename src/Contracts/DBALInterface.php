<?php

namespace Pkg6\DBALW\Contracts;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Pkg6\DBALW\Builder\ShowCreateTableAbstract;
use Pkg6\DBALW\DBAL;

interface DBALInterface
{
    /**
     * @return array
     */
    public function getParams();

    /**
     * @return Connection
     */
    public function getConnection();

    /**
     * @param DriverInterface $driver
     * @return $this
     */
    public function setDriver(DriverInterface $driver);

    /**
     * @return DriverInterface
     */
    public function driver();

    /**
     * @return QueryBuilder
     */
    public function newQuery();

    /**
     * @param string $tableName
     * @param callable $callable
     * @param int $count
     * @param string $column
     * @param string $order
     * @param callable|null $queryCallable
     * @param string|string[]|null $select
     * @return bool
     * @throws Exception
     */
    public function chunk($tableName, callable $callable, int $count = 10, string $column = "", string $order = "asc", callable $queryCallable = null, $select = "*");

    /**
     * @param string $tableName
     * @param int $page
     * @param int $count
     * @param callable|null $queryBuilderCallable
     * @param string|string[]|null $select
     * @return \Generator|int
     * @throws Exception
     */
    public function yieldTableDataByPage(string $tableName, int $page = 1, int $count = 100, callable $queryBuilderCallable = null, $select = "*");

    /**
     * 执行sql语句
     * @param string $sql
     * @return mixed
     */
    public function execute(string $sql);

    /**
     * 关闭连接
     * @return void
     */
    public function close();
}