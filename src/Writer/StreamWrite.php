<?php

namespace Pkg6\DBALW\Writer;

use InvalidArgumentException;
use Pkg6\DBALW\Abstracts\WriteAbstract;
use Pkg6\DBALW\Contracts\WriteTypeInterface;
use RuntimeException;

class StreamWrite extends WriteAbstract
{
    /**
     * @var mixed|string
     */
    protected $stream;

    protected $readMaxLine = 1000;

    /**
     * @param string $stream
     */
    public function __construct(string $stream)
    {
        $this->stream = $stream;
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
     * @return \Generator
     */
    public function readSQL()
    {
        $stream = fopen($this->stream, 'r');
        $sql = '';
        $currentLine = 0;
        while (!feof($stream)) {
            $sql .= fgets($stream);
            $currentLine++;
            if ($currentLine % $this->readMaxLine == 0) {
                yield $sql;
                $sql = '';
            }
        }
        if ($sql != '') {
            yield $sql;
        }
        fclose($stream);
    }

    /**
     * @param string $sql
     * @return void
     */
    public function writeSQLString(string $sql)
    {
        $stream = $this->createWriteStream();
        flock($stream, LOCK_EX);
        if (fwrite($stream, $sql . PHP_EOL) === false) {
            flock($stream, LOCK_UN);
            fclose($stream);
            throw new RuntimeException(sprintf(
                'Unable to write due to an error writing to the stream: %s',
                error_get_last()['message'] ?? '',
            ));
        }
        $this->stream = stream_get_meta_data($stream)['uri'];
        flock($stream, LOCK_UN);
        fclose($stream);
    }


    /**
     * @return resource
     */
    protected function createWriteStream()
    {
        $stream = $this->stream;
        if (is_string($stream)) {
            $base_path = pathinfo($stream, PATHINFO_DIRNAME);
            if (!is_dir($base_path)) {
                mkdir($base_path, 0775, true);
            }
            $stream = @fopen($stream, 'ab');
            if ($stream === false) {
                throw new RuntimeException(sprintf(
                    'The "%s" stream cannot be opened.',
                    (string)$this->stream,
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