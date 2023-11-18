<?php

namespace Pkg6\DBALW\Scheduler;

use Psr\Log\LoggerInterface;

abstract class JobAbstract
{

    abstract public function handle(GeneratorScheduler $scheduler, $taskID);
}