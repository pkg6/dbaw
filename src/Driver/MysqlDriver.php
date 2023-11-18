<?php

namespace Pkg6\DBALW\Driver;

use Doctrine\DBAL\Exception;
use Generator;
use Pkg6\DBALW\Builder\ShowCreateTableAbstract;
use Pkg6\DBALW\Contracts\DBALInterface;
use Pkg6\DBALW\Contracts\DriverInterface;

class MysqlDriver implements DriverInterface
{
    /**
     * @var string
     */
    public const SELECTFIELD = "*";
    /**
     * @var string
     */
    protected $tableTypeTable = "BASE TABLE";
    /**
     * @var string
     */
    protected $tableTypeView = "VIEW";
    /**
     * @var DBALInterface
     */
    protected $dbal;

    /**
     * @param DBALInterface $dbal
     * @return void
     */
    public function setDBAL(DBALInterface $dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * 清空表数据
     * @param $tableName
     * @return int|string
     * @throws Exception
     */
    public function truncate($tableName)
    {
        return $this->dbal->execute("TRUNCATE `{$tableName}`");
    }

    /**
     * 优化表
     * @param string|array $tableName
     * @return int|string
     */
    public function optimize($tableName)
    {
        return $this->dbal->execute("OPTIMIZE TABLE `{$tableName}`");
    }

    /**
     * 修复表
     * @param string|array $tableName
     * @return int|string
     */
    public function repair($tableName)
    {
        return $this->dbal->execute("REPAIR TABLE `{$tableName}`");
    }

    /**
     * 查询表结构并生成sql语句
     * @param string $tableName
     * @return ShowCreateTableAbstract
     * @throws Exception
     */
    public function SQLCreateTable(string $tableName)
    {
        $statement = $this->dbal->getConnection()->prepare(sprintf('SHOW CREATE TABLE `%s`', $tableName));
        $resultSet = $statement->executeQuery();
        $data = $resultSet->fetchAssociative();
        return new ShowCreateTableAbstract($data);
    }

    /**
     * @param $tableName
     * @return string
     * @throws Exception
     */
    public function pk($tableName)
    {
        $pks = $this->dbal->getConnection()->createSchemaManager()
            ->introspectTable($tableName)
            ->getPrimaryKey()
            ->getUnquotedColumns();
        return $pks[0] ?? "";
    }

    /**
     * 获取所有的数据表
     * @return string[]
     * @throws Exception
     */
    public function tables()
    {
        $statement = $this->dbal->getConnection()->prepare(sprintf("SHOW FULL TABLES WHERE table_type = '%s'", $this->tableTypeTable));
        $resultSet = $statement->executeQuery();
        return $resultSet->fetchFirstColumn();
    }


    /**
     * 获取表字段
     * @param $tableName
     * @return string[]
     * @throws Exception
     */
    public function fields($tableName)
    {
        $ret = $this->tableColumns($tableName);
        return array_column($ret, "Field");
    }

    /**
     * 通过SHOW FULL COLUMNS获取表结构详细信息
     * @param string $tableName
     * @return \mixed[][]
     * @throws Exception
     */
    public function tableFullColumns(string $tableName)
    {
        $statement = $this->dbal->getConnection()->prepare(sprintf('SHOW FULL COLUMNS FROM `%s`', $tableName));
        $resultSet = $statement->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    /**
     * 通过SHOW COLUMNS 获取表详细信息
     * @param string $tableName
     * @return \mixed[][]
     * @throws Exception
     */
    public function tableColumns(string $tableName)
    {
        $statement = $this->dbal->getConnection()->prepare(sprintf('SHOW COLUMNS FROM `%s`', $tableName));
        $resultSet = $statement->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    /**
     * 获取表结构
     * @param string $tableName
     * @return array
     * @throws Exception
     */
    public function tableDesc(string $tableName)
    {
        $statement = $this->dbal->getConnection()->prepare(sprintf('DESC `%s`', $tableName));
        $resultSet = $statement->executeQuery();
        return $resultSet->fetchAllAssociative();
    }


    /**
     * 获取所有视图
     * @return string[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function views()
    {
        $statement = $this->dbal->getConnection()->prepare(sprintf("SHOW FULL TABLES WHERE table_type = '%s'", $this->tableTypeView));
        $resultSet = $statement->executeQuery();
        return $resultSet->fetchFirstColumn();
    }


}