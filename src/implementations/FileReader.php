<?php

namespace aigletter\logging\implementations;

class FileReader implements \Iterator
{
    protected $handle;

    protected $index;

    protected $lines;

    protected $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->handle = @fopen($filename, "r");
    }

    public function __destruct()
    {
        @fclose($this->handle);
    }
    public function current(): mixed
    {
        return fgets($this->handle);
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return !feof($this->handle);
    }

    public function rewind(): void
    {
        rewind($this->handle);

        $this->index = 0;
    }
}