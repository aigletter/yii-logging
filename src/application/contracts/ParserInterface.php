<?php

namespace aigletter\logging\application\contracts;

use aigletter\logging\application\dto\LogDto;

interface ParserInterface
{
    public function parse(string $line): LogDto;
}