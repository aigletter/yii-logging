<?php

namespace aigletter\logging\contracts;

interface FileInterface extends \Iterator
{
    public function open(string $filename): bool;
}