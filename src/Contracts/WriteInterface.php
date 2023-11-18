<?php

namespace Pkg6\DBALW\Contracts;

use Pkg6\DBALW\Abstracts\WriteAbstract;

interface WriteInterface extends TableInterface
{


    /**
     * @param DBALWriterInterface $writer
     * @return $this
     */
    public function setDBALWriter(DBALWriterInterface $writer);

    /**
     * @return DBALWriterInterface
     */
    public function getDBALWriter();

    /**
     * @param WriteRuleInterfce $writeRule
     * @return $this
     */
    public function setWriteRule(WriteRuleInterfce $writeRule);

    /**
     * 当返回true时不执行writeData方法
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return bool
     */
    public function doWriteRule($data, WriteTypeInterface $writeType);

    /**
     * @param WriteExecuteExceptionInterface|null $writerException
     * @return $this
     */
    public function setWriteExecuteException(WriteExecuteExceptionInterface $writerException = null);

    /**
     * @return WriteExecuteExceptionInterface
     */
    public function getWriteExecuteException();

    /**
     * @param WriteDataExceptionInterface $writerException
     * @return $this
     */
    public function setWriterException(WriteDataExceptionInterface $writeDataException);

    /**
     * @return WriteDataExceptionInterface
     */
    public function getWriterException();

    /**
     * @param array|string $data
     * @param WriteTypeInterface $writeType
     * @return void
     */
    public function writeData($data, WriteTypeInterface $writeType);

    /**
     * @return \Generator
     */
    public function readSQL();
}