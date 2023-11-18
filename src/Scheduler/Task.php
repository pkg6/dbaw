<?php

namespace Pkg6\DBALW\Scheduler;

use Generator;

class Task
{
    /**
     * @var
     */
    protected $taskID;
    /**
     * @var Generator
     */
    protected $coroutine;
    /**
     * @var null
     */
    protected $sendValue = null;
    /**
     * @var bool
     */
    protected $beforeFirstYield = true;

    /**
     * @param $taskID
     * @param Generator $coroutine
     */
    public function __construct($taskID, Generator $coroutine)
    {
        $this->taskID    = $taskID;
        $this->coroutine = $coroutine;
    }

    /**
     * @return mixed
     */
    public function getTaskID()
    {
        return $this->taskID;
    }

    /**
     * @param $sendValue
     * @return void
     */
    public function setSendValue($sendValue)
    {
        $this->sendValue = $sendValue;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            return $this->coroutine->current();
        } else {
            $this->sendValue = null;
            return $this->coroutine->send($this->sendValue);
        }
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}