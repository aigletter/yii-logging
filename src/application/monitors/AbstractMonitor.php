<?php

namespace aigletter\logging\application\monitors;

use aigletter\logging\application\contracts\MonitorInterface;
use aigletter\logging\application\contracts\ReaderInterface;
use aigletter\logging\application\contracts\StorageInterface;

abstract class AbstractMonitor implements MonitorInterface
{
    protected ReaderInterface $reader;

    protected StorageInterface $storage;

    public function __construct(
        ReaderInterface $reader,
        StorageInterface $storage,
    ) {
        $this->reader = $reader;
        $this->storage = $storage;
    }
}