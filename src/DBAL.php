<?php

namespace Pkg6\DBALW;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Tools\DsnParser;
use Pkg6\DBALW\Concern\DBALYieldData;
use Pkg6\DBALW\Contracts\DBALInterface;
use Pkg6\DBALW\Contracts\DriverInterface;
use Pkg6\DBALW\Driver\MysqlDriver;

class DBAL implements DBALInterface
{
    use DBALYieldData;
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
     * @param mixed $connectionParams
     * @throws Exception
     */
    public function __construct($connectionParams)
    {
        if (is_string($connectionParams)) {
            $connectionParams = (new DsnParser())->parse($connectionParams);
        }
        $this->params = $connectionParams;
        $this->connection = DriverManager::getConnection($this->params);
    }

    /**
     * @param DriverInterface $driver
     * @return $this
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return DriverInterface
     */
    public function driver()
    {
        if (is_null($this->driver)) {
            $this->driver = new MysqlDriver();
        }
        $this->driver->setDBAL($this);
        return $this->driver;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * 获取连接
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }


    /**
     * @return QueryBuilder
     */
    public function newQuery()
    {
        return $this->connection->createQueryBuilder();
    }


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
    public function chunk($tableName, callable $callable, int $count = 10, string $column = "", string $order = "asc", callable $queryCallable = null, $select = "*")
    {
        $pk = $column ?: $this->driver()->pk($tableName);
        $queryBuilder = $this->newQuery()
            ->select($select)
            ->from($tableName)
            ->setMaxResults($count);
        if (!is_null($queryCallable)) {
            $queryCallable($queryBuilder);
        }
        if (empty($pk)) {
            $page = 1;
        }
        if (isset($page)) {
            $offset = ($page - 1) * $count;
            $queryBuilder->setFirstResult($offset);
        } else {
            $queryBuilder->orderBy($pk, $order);
        }
        $resetSet = $queryBuilder->fetchAllAssociative();
        while (count($resetSet) > 0) {
            if ($callable($tableName, $resetSet) === false) {
                return false;
            }
            if (isset($page)) {
                $page++;
                $resetSet = $queryBuilder->setFirstResult($page)->fetchAllAssociative();
            } else {
                $opt = strtolower($order) === "asc" ? ">" : "<";
                $end = end($resetSet);
                $lastID = $end[$pk];
                $resetSet = $queryBuilder->andWhere("{$pk}{$opt}{$lastID}")->fetchAllAssociative();
            }
        }
        return true;
    }

    /**
     * @param string $sql
     * @return int
     * @throws Exception
     */
    public function execute(string $sql)
    {
        $statement = $this->connection->prepare($sql);
        $resultSet = $statement->executeQuery();
        return $resultSet->rowCount();
    }


    /**
     * @return void
     */
    public function close()
    {
        $this->connection->close();
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->close();
    }

    /**
     * 关闭数据库连接
     */
    public function __destruct()
    {
        $this->close();
    }
}