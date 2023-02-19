<?php

namespace aigletter\logging\contracts;

use aigletter\logging\dto\LogDto;

interface ParserInterface
{
    public function parse(string $line): LogDto;
}