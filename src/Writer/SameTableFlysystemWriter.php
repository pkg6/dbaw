<?php

namespace Pkg6\DBALW\Writer;

use Exception;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use Pkg6\DBALW\Contracts\DBALInterface;
use Pkg6\DBALW\Contracts\WriteDataExceptionInterface;
use Pkg6\DBALW\Contracts\WriteExecuteExceptionInterface;
use Pkg6\DBALW\Contracts\WriteTypeInterface;
use Pkg6\DBALW\Builder;
use Pkg6\DBALW\Support\Http;
use Pkg6\DBALW\Support\Str;

class SameTableFlysystemWriter extends SameTableWrite
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * ["table1"=>["file1","file2"]]
     * @var array
     */
    protected $tablesFields = [];

    /**
     * 文件访问地址
     * @var string
     */
    protected $host;

    /**
     * @param DBALInterface $dbal
     * @param FilesystemAdapter $adapter
     * @param array $tablesFields
     * @param array $host
     * @param WriteDataExceptionInterface|null $writerException
     */
    public function __construct(DBALInterface $dbal, FilesystemAdapter $adapter, $tablesFields, $host, WriteDataExceptionInterface $writerException = null)
    {
        $this->filesystem   = new Filesystem($adapter);
        $this->tablesFields = $tablesFields;
        $this->host         = $host;
        $this->setWriterException($writerException);
        parent::__construct($dbal);
    }


    /**
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return void
     * @throws \League\Flysystem\FilesystemException
     */
    public function writeData($data, WriteTypeInterface $writeType)
    {
        if ($this->upload($data)) {
            parent::writeData($data, $writeType);
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws \League\Flysystem\FilesystemException
     */
    protected function upload($data)
    {
        $fields = $this->yieldFieldsURL($data);
        foreach ($fields as $locationURL) {
            try {
                [$field, $location, $url] = $locationURL;
                $contents = Http::get($url);
                $this->filesystem->write($location, $contents);
            } catch (Exception $exception) {
                return $this->getWriteExecuteException()->handler(
                    $this->getTableName(),
                    $data,
                    $field,
                    $this->getDBALWriter()->Logger(),
                    $exception,
                );
            }
        }
        return true;
    }


    /**
     * @param $data
     * @return \Generator
     */
    public function yieldFieldsURL($data)
    {
        if (isset($this->tablesFields[$this->getTableName()])) {
            $fields = $this->tablesFields[$this->getTableName()];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $val = $data[$field];
                    if (Str::startsWith($val, $this->host)) {
                        yield [$field, Str::replaceFirst($this->host, "", $val), $val];
                    } else {
                        yield [$field, $val, $this->host . $val];
                    }
                }
            }
        }
    }
}