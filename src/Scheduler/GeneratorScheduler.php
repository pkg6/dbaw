<?php

namespace Pkg6\DBALW\Scheduler;

use Generator;
use SplQueue;

class GeneratorScheduler
{
    /**
     * @var int
     */
    protected $maxTaskId = 0;
    /**
     * @var SplQueue
     */
    protected $taskQueue;

    /**
     * @var array
     */
    protected $taskMap = [];


    public function __construct()
    {
        $this->taskQueue = new SplQueue();
    }

    /**
     * @param Generator $coroutine
     * @return int
     */
    public function push(Generator $coroutine)
    {
        $tid                 = ++$this->maxTaskId;
        $task                = new Task($tid, $coroutine);
        $this->taskMap[$tid] = $task;
        $this->schedule($task);
        return $tid;
    }

    /**
     * @param $tid
     * @return bool
     */
    public function pull($tid)
    {
        if (!isset($this->taskMap[$tid])) {
            return false;
        }
        unset($this->taskMap[$tid]);
        foreach ($this->taskQueue as $i => $task) {
            if ($task->getTaskId() === $tid) {
                unset($this->taskQueue[$i]);
                break;
            }
        }
        return true;
    }

    /**
     * @param Task $task
     * @return void
     */
    public function schedule(Task $task)
    {
        $this->taskQueue->enqueue($task);
    }

    /**
     * @return void
     */
    public function run()
    {
        while (!$this->taskQueue->isEmpty()) {
            /** @var $task Task */
            $task = $this->taskQueue->dequeue();
            $ret  = $task->run();
            if ($ret instanceof JobAbstract) {
                $ret->handle($this,$task->getTaskId());
            }
            if ($task->isFinished()) {
                unset($this->taskMap[$task->getTaskId()]);
            } else {
                $this->schedule($task);
            }
        }
    }
}