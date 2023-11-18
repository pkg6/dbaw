<?php

namespace Pkg6\DBALW\Writer;

use Pkg6\DBALW\Abstracts\WriteAbstract;
use Pkg6\DBALW\Contracts\DBALInterface;
use Pkg6\DBALW\Contracts\WriteTypeInterface;
use Pkg6\DBALW\Rule\WriterIgnoreTable;
use RuntimeException;

class SameTableWrite extends WriteAbstract
{
    /**
     * @var DBALInterface
     */
    protected $dbal;


    /**
     * @param DBALInterface $dbal
     * @param array $ignoreTable
     */
    public function __construct(DBALInterface $dbal, $ignoreTable = [])
    {
        $this->dbal = $dbal;
        $this->setWriteRule(new WriterIgnoreTable($ignoreTable));
    }

    /**
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute($data, WriteTypeInterface $writeType)
    {
        if ($writeType->getType() === WriteTypeInterface::TABLEDATA) {
            $sql = $this->getDBALWriter()
                ->getInsertMode()
                ->dataSQL($this->getTableName(), $data);
        } else {
            $sql = $data;
        }
        $this->dbal->execute($sql);
    }

    /**
     * @return \Generator
     * @throws RuntimeException
     */
    public function readSQL()
    {
        throw new RuntimeException("Reverse reading of information from the entire library is not supported");
    }
}