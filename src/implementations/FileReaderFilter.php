<?php

namespace aigletter\logging\implementations;

use aigletter\logging\contracts\FileReaderInterface;

class FileReaderFilter extends \FilterIterator implements FileReaderInterface
{
    public function accept(): bool
    {
        return $this->current() != false;
    }
}