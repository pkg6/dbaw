<?php

namespace Pkg6\DBALW\Contracts;

use Exception;
use Psr\Log\LoggerInterface;

interface WriteExecuteExceptionInterface
{

    /**
     * 接受到异常信息，返回true表示抛出异常等于终止服务，否则就继续执行
     * @param string $tableName
     * @param array $data
     * @param string $field
     * @param LoggerInterface $logger
     * @param Exception $exception
     * @return bool
     */
    public function handler($tableName, $data, $field, LoggerInterface $logger, Exception $exception);

}