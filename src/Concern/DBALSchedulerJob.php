<?php

namespace Pkg6\DBALW\Concern;

use Pkg6\DBALW\Scheduler\ReadJob;
use Pkg6\DBALW\Scheduler\WriteJob;

trait DBALSchedulerJob
{
    /**
     * @param $method
     * @return \Generator
     * @throws \Doctrine\DBAL\Exception
     */
    public function generatorWriteJob($method)
    {
        $tables = $this->dbal->driver()->tables();
        foreach ($tables as $table) {
            yield new WriteJob($this, $table, $method);
        }
    }

    public function generatorReadJob()
    {
        foreach ($this->write->readSQL() as $sql) {
            yield new ReadJob($this, $sql);
        }
    }
}