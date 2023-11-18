<?php

namespace Pkg6\DBALW\Contracts;

use Generator;
use Psr\Log\LoggerInterface;

interface DBALWriterInterface
{

    public const INSERT = 1;
    public const REPLACE = 2;

    //只写入表结构
    public const GeneratorJobMethodWriteTableStructure = 1;
    //写入数据
    public const GeneratorJobMethodWriteTableData = 2;
    //迁移表结构和表数据
    public const GeneratorJobMethodMigrate = 3;
    //修复表
    public const GeneratorJobMethodREPAIR = 4;
    //优化表
    public const GeneratorJobMethodOPTIMIZE = 5;

    /**
     * @return DBALInterface
     */
    public function getDBAL();

    /**
     * @return SchedulerInterface
     */
    public function scheduler();

    /**
     * @param int $method
     * @return Generator
     */
    public function generatorWriteJob($method);

    /**
     * @return Generator
     */
    public function generatorReadJob();

    /**
     * @param WriteInterface $write
     * @return $this
     */
    public function setWrite(WriteInterface $write);

    /**
     * @return WriteInterface
     */
    public function getWrite();

    /**
     * 设置插入语句INSERT INTO 或REPLACE INTO
     * @param InsertModeInterface $insertMode
     * @return $this
     * @see DBALWriterInterface::REPLACE
     * @see DBALWriterInterface::INSERT
     */
    public function setInsertMode(InsertModeInterface $insertMode);

    /**
     * @return InsertModeInterface
     */
    public function getInsertMode();

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @param $level
     * @param $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = []);

    /**
     * 写入表结构
     * @param $tableName
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function writeTableStructure($tableName);

    /**
     * 写入表数据
     * @param $tableName
     * @param int $count
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function writeTableAllData($tableName, int $count = 100);

    /**
     * 分页写入表数据
     * @param string $tableName
     * @param int $page
     * @param int $count
     * @return mixed
     */
    public function writeTableData($tableName, $page = 1, int $count = 100);
}