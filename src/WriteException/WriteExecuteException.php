<?php

namespace Pkg6\DBALW\WriteException;

use Exception;
use Pkg6\DBALW\Contracts\WriteExecuteExceptionInterface;
use Psr\Log\LoggerInterface;

class WriteExecuteException implements WriteExecuteExceptionInterface
{
    /**
     * @param string $tableName
     * @param array|string $data
     * @param string $field
     * @param LoggerInterface $logger
     * @param Exception $exception
     * @return bool
     */
    public function handler($tableName, $data, $field, LoggerInterface $logger, Exception $exception)
    {
        return false;
    }
}