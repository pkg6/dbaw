<?php

namespace Pkg6\DBALW\Scheduler;

use Pkg6\DBALW\Contracts\DBALWriterInterface;
use Pkg6\Log\LoggerTrait;
use Psr\Log\LogLevel;

class ReadJob extends JobAbstract
{
    /**
     * @var DBALWriterInterface
     */
    protected $writer;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @param DBALWriterInterface $writer
     * @param $sql
     */
    public function __construct(DBALWriterInterface $writer, $sql)
    {
        $this->sql = $sql;
        $this->writer = $writer;
    }

    /**
     * @param GeneratorScheduler $scheduler
     * @param $taskID
     * @return void
     */
    public function handle(GeneratorScheduler $scheduler, $taskID)
    {
        $this->writer->log(LogLevel::INFO, "Restore SQL : " . $this->sql);
        $this->writer->getDBAL()->execute($this->sql);
    }
}