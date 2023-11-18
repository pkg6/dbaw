<?php

namespace Pkg6\DBALW\Scheduler;

use Exception;
use Pkg6\DBALW\Contracts\DBALWriterInterface;
use Psr\Log\LogLevel;

class WriteJob extends JobAbstract
{

    /**
     * @var DBALWriterInterface
     */
    protected $writer;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var string
     */
    protected $method;

    /**
     * @param DBALWriterInterface $writer
     * @param string $table
     * @param string $method
     */
    public function __construct(DBALWriterInterface $writer, $table, $method)
    {
        $this->table = $table;
        $this->method = $method;
        $this->writer = $writer;
    }

    /**
     * @param GeneratorScheduler $scheduler
     * @param $taskID
     * @return void
     */
    public function handle(GeneratorScheduler $scheduler, $taskID)
    {
        try {
            switch ($this->method) {
                case DBALWriterInterface::GeneratorJobMethodWriteTableStructure:
                    $this->writer->log(LogLevel::INFO, "Write Table Structure : " . $this->table);
                    $this->writer->writeTableStructure($this->table);
                    break;
                case DBALWriterInterface::GeneratorJobMethodWriteTableData:
                    $this->writer->log(LogLevel::INFO, "Write Table Data : " . $this->table);
                    $this->writer->writeTableAllData($this->table);
                    break;
                case DBALWriterInterface::GeneratorJobMethodMigrate:
                    $this->writer->log(LogLevel::INFO, "Write Table Structure : " . $this->table);
                    if ($this->writer->writeTableStructure($this->table)) {
                        $this->writer->log(LogLevel::INFO, "Write Table Data : " . $this->table);
                        $this->writer->writeTableAllData($this->table);
                    }
                    break;
                case DBALWriterInterface::GeneratorJobMethodREPAIR:
                    $this->writer->getDBAL()->driver()->repair($this->table);
                    $this->writer->log(LogLevel::INFO, "Repair Table : " . $this->table);
                    break;
                case DBALWriterInterface::GeneratorJobMethodOPTIMIZE:
                    $this->writer->getDBAL()->driver()->optimize($this->table);
                    $this->writer->log(LogLevel::INFO, "OPTIMIZE TABLE : " . $this->table);
                    break;
                default:
                    $this->writer->log(LogLevel::ERROR, "There are no actionable actions : " . $this->table);
            }
        } catch (Exception $exception) {
            $this->writer->log(LogLevel::ERROR, "Write exception ", [$exception]);
        }

    }
}