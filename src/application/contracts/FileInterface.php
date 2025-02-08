<?php

namespace aigletter\logging\application\contracts;

interface FileInterface extends \Iterator
{
    public function open(string $filename): bool;
}