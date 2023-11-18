<?php

namespace Pkg6\DBALW\Contracts;

use Doctrine\DBAL\Exception;
use Pkg6\DBALW\Builder\ShowCreateTableAbstract;

interface DriverInterface
{
    /**
     * @param DBALInterface $dbal
     * @return mixed
     */
    public function setDBAL(DBALInterface $dbal);

    /**
     * 获取所有的数据表
     * @return string[]
     * @throws Exception
     */
    public function tables();

    /**
     * 表主键
     * @param $tableName
     * @return mixed
     */
    public function pk($tableName);
    /**
     * 获取表字段
     * @param $tableName
     * @return string[]
     * @throws Exception
     */
    public function fields($tableName);

    /**
     * 获取所有视图
     * @return string[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function views();


    /**
     * 查询表结构并生成sql语句
     * @param string $tableName
     * @return ShowCreateTableAbstract
     * @throws Exception
     */
    public function SQLCreateTable(string $tableName);

    /**
     * 清空表数据
     * @param $tableName
     * @return int|string
     * @throws Exception
     */
    public function truncate($tableName);

    /**
     * 优化表
     * @param string|array $tableName
     * @return int|string
     */
    public function optimize($tableName);

    /**
     * 修复表
     * @param string|array $tableName
     * @return int|string
     */
    public function repair($tableName);
}