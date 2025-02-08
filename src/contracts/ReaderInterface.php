<?php

namespace aigletter\logging\contracts;

use aigletter\logging\dto\LogDto;

interface ReaderInterface
{
    /**
     * @param string $filename
     * @return bool
     */
    public function open(string $filename): bool;

    /**
     * @return LogDto|null
     */
    public function read(): ?LogDto;

    /**
     * @param int $limit
     * @return array
     */
    public function chunk(int $limit): array;
}