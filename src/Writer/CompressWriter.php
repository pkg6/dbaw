<?php

namespace Pkg6\DBALW\Writer;

use InvalidArgumentException;
use Pkg6\DBALW\Abstracts\WriteAbstract;
use Pkg6\DBALW\Contracts\WriteTypeInterface;
use RuntimeException;

class CompressWriter extends WriteAbstract
{

    /**
     * @var string
     */
    protected $file;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @param $data
     * @param WriteTypeInterface $writeType
     * @return void
     */
    public function execute($data, WriteTypeInterface $writeType)
    {
        switch ($writeType->getType()) {
            case WriteTypeInterface::TABLEDATA:
                $sql = $this->getDBALWriter()
                    ->getInsertMode()
                    ->dataSQL($this->getTableName(), $data);
                $this->writeSQLString($sql);
                break;
            default:
                $this->writeSQLString($data);
        }
    }

    /**
     * @return \Generator|void
     */
    public function readSQL()
    {
        $stream = gzopen($this->file, 'r');
        $sql    = "";
        while (!gzeof($stream)) {
            $sql .= gzgets($stream);
            yield trim($sql);
        }
        gzclose($stream);
    }

    /**
     * @param string $sql
     * @return void
     */
    protected function writeSQLString(string $sql)
    {
        $stream = $this->createWriteStream();
        flock($stream, LOCK_EX);
        if (gzwrite($stream, $sql . PHP_EOL) === false) {
            flock($stream, LOCK_UN);
            gzclose($stream);
            throw new RuntimeException(sprintf(
                'Unable to write due to an error writing to the stream: %s',
                error_get_last()['message'] ?? '',
            ));
        }
        flock($stream, LOCK_UN);
        gzclose($stream);
    }

    /**
     * @return resource
     */
    protected function createWriteStream()
    {
        $stream = $this->file;
        if (is_string($stream)) {
            $stream = @gzopen($stream, 'ab');
            if ($stream === false) {
                throw new RuntimeException(sprintf(
                    'The "%s" stream cannot be opened.',
                    (string)$this->file,
                ));
            }
        }
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new InvalidArgumentException(sprintf(
                'Invalid stream provided. It must be a string stream identifier or a stream resource, "%s" received.',
                gettype($stream),
            ));
        }
        return $stream;
    }
}