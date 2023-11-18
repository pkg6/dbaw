<?php

namespace Pkg6\DBALW\WriteException;

use Exception;
use Pkg6\DBALW\Contracts\WriteDataExceptionInterface;
use Psr\Log\LoggerInterface;

class WriteDataException implements WriteDataExceptionInterface
{

    /**
     * @param $tableName
     * @param $data
     * @param LoggerInterface $logger
     * @param Exception $exception
     * @return bool
     */
    public function handler($tableName, $data, LoggerInterface $logger, Exception $exception)
    {
        return false;
    }
}