<?php

namespace aigletter\logging\infrastructure\implementations;

use aigletter\logging\application\contracts\FileInterface;

class File implements FileInterface
{
    protected $handle;

    /**
     * @var int
     */
    protected int $index;

    /**
     * @param string $filename
     * @return bool
     */
    public function open(string $filename): bool
    {
        if ($this->handle) {
            fclose($this->handle);
        }

        $this->handle = @fopen($filename, "r");
        $this->index = 0;

        return (bool) $this->handle;
    }

    /**
     * @return mixed
     */
    public function current(): mixed
    {
        return fgets($this->handle);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * @return mixed
     */
    public function key(): mixed
    {
        return $this->index;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function valid(): bool
    {
        if (!isset($this->handle)) {
            throw new \Exception('The file must be opened first');
        }

        return !feof($this->handle);
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        rewind($this->handle);

        $this->index = 0;
    }
}