<?php

namespace aigletter\logging\application\services;

use aigletter\logging\application\contracts\FileInterface;
use aigletter\logging\application\contracts\ParserInterface;
use aigletter\logging\application\contracts\ReaderInterface;
use aigletter\logging\application\dto\LogDto;

class Reader implements ReaderInterface
{
    /**
     * @var FileInterface
     */
    private FileInterface $file;

    /**
     * @var ParserInterface
     */
    private ParserInterface $parser;

    /**
     * @param FileInterface $file
     * @param ParserInterface $parser
     */
    public function __construct(FileInterface $file, ParserInterface $parser)
    {
        $this->file = $file;
        $this->parser = $parser;
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function open(string $filename): bool
    {
        $result = $this->file->open($filename);
        $this->file->rewind();

        return $result;
    }

    /**
     * @return LogDto|null
     */
    public function read(): ?LogDto
    {
        if (!$this->file->valid()) {
            return null;
        }

        $row = $this->file->current();
        if (!$row) {
            return null;
        }

        $dto = $this->parse($row);

        $this->file->next();

        return $dto;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function chunk(int $limit): array
    {
        $chunk = [];
        while ($this->file->valid()) {
            $row = $this->file->current();
            if (!$row) {
                break;
            }
            $chunk[] = $this->parse($row);
            $this->file->next();
            if (count($chunk) === $limit) {
                break;
            }
        }

        return $chunk;
    }

    /**
     * @param string $row
     * @return LogDto
     */
    private function parse(string $row): LogDto
    {
        $dto = $this->parser->parse($row);
        $dto->id = md5($row);

        return $dto;
    }
}