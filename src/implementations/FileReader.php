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
    public function current()
    {
        return fgets($this->handle);
    }

    public function next()
    {
        $this->index++;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return !feof($this->handle);
    }

    public function rewind()
    {
        rewind($this->handle);

        $this->index = 0;
    }
}