<?php

namespace Pkg6\DBALW;

use Doctrine\DBAL\Query\QueryBuilder;
use Pkg6\DBALW\Builder\InsertBuilder;
use Pkg6\DBALW\Builder\ShowCreateTableAbstract;
use Pkg6\DBALW\Concern\DBALSchedulerJob;
use Pkg6\DBALW\Contracts\DBALInterface;
use Pkg6\DBALW\Contracts\SchedulerInterface;
use Pkg6\DBALW\Contracts\DBALWriterInterface;
use Pkg6\DBALW\Contracts\InsertModeInterface;
use Pkg6\DBALW\Contracts\WriteInterface;
use Pkg6\DBALW\Contracts\WriteTypeInterface;
use Pkg6\DBALW\Rule\WriterIgnoreTable;
use Pkg6\DBALW\Scheduler\DBALScheduler;
use Pkg6\Log\handler\StreamHandler;
use Pkg6\Log\Logger;
use Psr\Log\LoggerInterface;

class DBALWriter implements DBALWriterInterface
{
    use DBALSchedulerJob;

    /**
     * @var string
     */
    public $defaultLoggerFile = "./runtime/log/%s.log";

    /**
     * @var array
     */
    protected $writeDynamic = [];
    /**
     * @var DBALInterface
     */
    protected $dbal;

    /**
     * @var WriteInterface
     */
    protected $write;

    /**
     * @var SchedulerInterface
     */
    protected $scheduler;

    /**
     * @var array
     */
    protected $ignoreTable = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var InsertModeInterface
     */
    protected $insertMode;

    /**
     * @var string
     */
    protected $insertModeMethod = InsertBuilder::METHODINSERTINTO;


    /**
     * @param DBALInterface $dbal
     * @param WriteInterface $write
     */
    public function __construct(DBALInterface $dbal, WriteInterface $write)
    {
        $this->dbal = $dbal;
        $this->setWrite($write);
    }

    /**
     * @return DBALInterface
     */
    public function getDBAL()
    {
        return $this->dbal;
    }

    /**
     * @param WriteInterface $write
     * @return $this
     */
    public function setWrite(WriteInterface $write)
    {
        $this->write = $write;
        return $this;
    }

    /**
     * @return WriteInterface
     */
    public function getWrite()
    {
        $this->write->setDBALWriter($this);
        if (!empty($this->ignoreTable)) {
            $this->write->setWriteRule(new WriterIgnoreTable($this->ignoreTable));
        }
        return $this->write;
    }


    /**
     * @param InsertModeInterface $insertMode
     * @return $this
     */
    public function setInsertMode(InsertModeInterface $insertMode)
    {
        $this->insertMode = $insertMode;
        return $this;
    }

    /**
     * @param $method
     * @return $this
     * @see InsertBuilder::METHODINSERTINTO
     * @see InsertBuilder::METHODINSERTIGNOREINTO
     * @see InsertBuilder::METHODREPLACEINTO
     */
    public function setInsertModeMethod($method)
    {
        $this->insertModeMethod = $method;
        return $this;
    }

    /**
     * @return InsertModeInterface
     */
    public function getInsertMode()
    {
        if (is_null($this->insertMode)) {
            $this->insertMode = new InsertMode();
        }
        $this->insertMode->setMethod($this->insertModeMethod);
        return $this->insertMode;
    }

    /**
     * 设置日志输出
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * 设置日志输出
     * @return LoggerInterface
     */
    public function Logger()
    {
        return $this->logger;
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (isset($this->logge)) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * @param SchedulerInterface $scheduler
     * @return $this
     */
    public function setScheduler(SchedulerInterface $scheduler)
    {
        $this->scheduler = $scheduler;
        return $this;
    }


    /**
     * @return SchedulerInterface
     */
    public function scheduler()
    {
        if (is_null($this->scheduler)) {
            $this->scheduler = new DBALScheduler();
        }
        $this->scheduler->setWriter($this);
        return $this->scheduler;
    }

    /**
     * @return array
     */
    public function getIgnoreTable()
    {
        return $this->ignoreTable;
    }

    /**
     * @param array $ignoreTable
     * @return $this
     */
    public function setIgnoreTable(array $ignoreTable)
    {
        $this->ignoreTable = $ignoreTable;
        return $this;
    }


    /**
     * 写入sql文件注释
     * @return void
     */
    public function writeInit()
    {
        $params = $this->dbal->getParams();
        $sql = "-- -----------------------------" . PHP_EOL;
        $sql .= "-- DBA BACKUP Transfer" . PHP_EOL;
        $sql .= "-- Host     : " . $params['host'] . "\n";
        $sql .= "-- Database : " . $params['dbname'] . "\n";
        $sql .= "-- Date : " . date("Y-m-d H:i:s") . PHP_EOL;
        $sql .= "-- -----------------------------" . PHP_EOL . PHP_EOL;
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;" . PHP_EOL;
        $this->writeDynamic("", WriteTypeInterface::ANNOTATION);
        $this->write->writeData($sql, new WriteType(WriteTypeInterface::ANNOTATION));
    }

    /**
     * 写入表数据
     * @param $tableName
     * @param int $count
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function writeTableAllData($tableName, int $count = 100)
    {
        $number = $this->writeTableData($tableName, 1, $count);
        while ($number != 0) {
            $number = $this->writeTableData($tableName, $number, $count);
        }
        return $number;
    }

    /**
     * 写入表结构
     * @param $tableName
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function writeTableStructure($tableName)
    {
        $tableStruct = $this->dbal->driver()->SQLCreateTable($tableName);
        $sql = PHP_EOL;
        $sql .= "-- -----------------------------" . PHP_EOL;
        $sql .= "-- Table structure for `{$tableName}`" . PHP_EOL;
        $sql .= "-- -----------------------------" . PHP_EOL;
        $sql .= "DROP TABLE IF EXISTS `{$tableName}`;" . PHP_EOL;
        $sql .= trim($tableStruct->toSQL()) . ";" . PHP_EOL . PHP_EOL;
        $this->writeDynamic($tableName, WriteTypeInterface::ANNOTATION);
        $this->write->setTableName($tableName)->writeData($sql, new WriteType(WriteTypeInterface::TABLESTRUCTURE));
        if ($tableStruct->type() == ShowCreateTableAbstract::VIEW) {
            return 0;
        }
        return 1;
    }

    /**
     * @param string $tableName
     * @param mixed $val
     * @param string $pk
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function writeTableDataByPk($tableName, $val, $pk = "")
    {
        if ($pk == "") {
            $pk = $this->dbal->driver()->pk($tableName);
        }
        $data = $this->dbal->newQuery()
            ->where("{$pk}={$val}")
            ->select("*")
            ->from($tableName)
            ->setMaxResults(1)
            ->fetchAssociative();
        $this->writeDynamic($tableName, WriteTypeInterface::TABLEDATA);
        $this->getWrite()->setTableName($tableName)->writeData($data, new WriteType(WriteTypeInterface::TABLEDATA));
    }

    /**
     * 分页写入表数据
     * @param $tableName
     * @param $page
     * @param int $count
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function writeTableData($tableName, $page = 1, int $count = 100)
    {
        $number = $this->dbal->yieldTableDataByPage($tableName, $page, $count);
        foreach ($number as $data) {
            if (is_array($data)) {
                $this->writeDynamic($tableName, WriteTypeInterface::TABLEDATA);
                $this->getWrite()
                    ->setTableName($tableName)
                    ->writeData($data, new WriteType(WriteTypeInterface::TABLEDATA));
            } else {
                return $data;
            }
        }
        return 0;
    }


    /**
     * @param string $table
     * @param int $writeType
     * @return void
     */
    protected function writeDynamic($table, $writeType)
    {
        if (isset($this->writeDynamic[$table][$writeType])) {
            $this->writeDynamic[$table][$writeType] += 1;
        } else {
            $this->writeDynamic[$table][$writeType] = 1;
        }
    }

    /**
     * @return array
     */
    public function getWriteDynamic()
    {
        return $this->writeDynamic;
    }
}