<?php

namespace Pkg6\DBALW\Abstracts;

use Pkg6\DBALW\Contracts\DBALWriterInterface;
use Pkg6\DBALW\Contracts\WriteDataExceptionInterface;
use Pkg6\DBALW\Contracts\WriteExecuteExceptionInterface;
use Pkg6\DBALW\Contracts\WriteInterface;
use Pkg6\DBALW\Contracts\WriteRuleInterfce;
use Pkg6\DBALW\Contracts\WriteTypeInterface;
use Pkg6\DBALW\WriteException\WriteDataException;
use Pkg6\DBALW\WriteException\WriteExecuteException;

abstract class WriteAbstract implements WriteInterface
{

    /**
     * @var string
     */
    protected $_tableName;

    /**
     * @var DBALWriterInterface
     */
    protected $_DBALWriter;

    /**
     * @var WriteExecuteExceptionInterface
     */
    protected $_writeExecuteException;

    /**
     * @var WriteDataExceptionInterface
     */
    protected $_writeDataException;

    /**
     * @var WriteRuleInterfce[]
     */
    protected $_writeRule = [];


    /**
     * @param DBALWriterInterface $writer
     * @return $this
     */
    public function setDBALWriter(DBALWriterInterface $writer)
    {
        $this->_DBALWriter = $writer;
        return $this;

    }

    /**
     * @return DBALWriterInterface
     */
    public function getDBALWriter()
    {
        return $this->_DBALWriter;
    }

    /**
     * @param $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * @param WriteExecuteExceptionInterface|null $writeExecuteException
     * @return WriteAbstract
     */
    public function setWriteExecuteException(WriteExecuteExceptionInterface $writeExecuteException = null)
    {
        $this->_writeExecuteException = $writeExecuteException;
        return $this;
    }

    /**
     * @return WriteExecuteExceptionInterface
     */
    public function getWriteExecuteException()
    {
        if (is_null($this->_writeExecuteException)) {
            $this->_writeExecuteException = new WriteExecuteException();
        }
        return $this->_writeExecuteException;
    }

    /**
     * @param WriteDataExceptionInterface $writeDataException
     * @return $this
     */
    public function setWriterException(WriteDataExceptionInterface $writeDataException)
    {
        $this->_writeDataException = $writeDataException;
        return $this;
    }


    /**
     * @return WriteDataExceptionInterface
     */
    public function getWriterException()
    {
        if (is_null($this->_writeDataException)) {
            $this->_writeDataException = new WriteDataException();
        }
        return $this->_writeDataException;
    }

    /**
     * @param WriteRuleInterfce $writeRule
     * @return $this
     */
    public function setWriteRule(WriteRuleInterfce $writeRule)
    {
        $this->_writeRule[] = $writeRule;
        return $this;
    }

    /**
     * 当返回true时不写入数据
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return bool
     */
    public function doWriteRule($data, WriteTypeInterface $writeType)
    {
        foreach ($this->_writeRule as $rule) {
            if ($rule->rule($this, $data, $writeType) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return mixed
     */
    abstract public function execute($data, WriteTypeInterface $writeType);

    /**
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return void
     * @throws \Exception
     */
    public function writeData($data, WriteTypeInterface $writeType)
    {
        if ($this->doWriteRule($data, $writeType) === true) {
            return;
        }
        try {
            $this->execute($data, $writeType);
        } catch (\Exception $exception) {
            if ($this->getWriterException()->handler(
                $this->getTableName(),
                $data,
                $this->getDBALWriter()->Logger(),
                $exception
            )) {
                throw $exception;
            };
        }
    }


}