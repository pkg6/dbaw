<?php

namespace Pkg6\DBALW\Scheduler;

use Pkg6\DBALW\Contracts\DBALWriterInterface;
use Pkg6\DBALW\Contracts\SchedulerInterface;

class DBALScheduler implements SchedulerInterface
{
    /**
     * @var DBALWriterInterface
     */
    protected $writer;

    /**
     * @param DBALWriterInterface $writer
     * @return $this
     */
    public function setWriter(DBALWriterInterface $writer)
    {
        $this->writer = $writer;
        return $this;
    }

    /**
     * 修复表
     * @return void
     */
    public function repair()
    {
        $scheduler = new GeneratorScheduler();
        $scheduler->push($this->writer->generatorWriteJob(DBALWriterInterface::GeneratorJobMethodREPAIR));
        $scheduler->run();
    }

    /**
     * 优化表
     * @return void
     */
    public function optimize()
    {
        $scheduler = new GeneratorScheduler();
        $scheduler->push($this->writer->generatorWriteJob(DBALWriterInterface::GeneratorJobMethodOPTIMIZE));
        $scheduler->run();
    }

    /**
     * 迁移结构
     * @return void
     */
    public function migrateStructure()
    {
        $scheduler = new GeneratorScheduler();
        $scheduler->push($this->writer->generatorWriteJob(DBALWriterInterface::GeneratorJobMethodWriteTableStructure));
        $scheduler->run();
    }

    /**
     * 迁移所有数据(不包含表结构)
     * @return void
     */
    public function migrateAllData()
    {
        $scheduler = new GeneratorScheduler();
        $scheduler->push($this->writer->generatorWriteJob(DBALWriterInterface::GeneratorJobMethodWriteTableData));
        $scheduler->run();
    }

    /**
     * 迁移表结构和表数据
     * @return void
     */
    public function migrate()
    {
        $scheduler = new GeneratorScheduler();
        $scheduler->push($this->writer->generatorWriteJob(DBALWriterInterface::GeneratorJobMethodMigrate));
        $scheduler->run();
    }

    /**
     * 恢复备份
     * @return void
     */
    public function restore()
    {
        $scheduler = new GeneratorScheduler();
        $scheduler->push($this->writer->generatorReadJob());
        $scheduler->run();
    }
}