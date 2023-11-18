<?php

namespace Pkg6\DBALW\Contracts;

use Exception;
use Psr\Log\LoggerInterface;

interface WriteDataExceptionInterface
{
    /**
     * 接受到异常信息，返回true表示跳过内容执行插入，返回false表示此数据不处理
     * @param string $tableName
     * @param array $data
     * @param string $field
     * @param LoggerInterface $logger
     * @param Exception $exception
     * @return bool
     */
    public function handler($tableName, $data, LoggerInterface $logger, Exception $exception);

}