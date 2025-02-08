<?php

namespace aigletter\logging\application\monitors;

use aigletter\logging\application\contracts\ReaderInterface;
use aigletter\logging\application\contracts\StorageInterface;

class BatchMonitor extends AbstractMonitor
{
    public const DEFAULT_BATCH_SIZE = 100;

    private int $batchSize;

    public function __construct(
        ReaderInterface $reader,
        StorageInterface $storage,
        ?int $batchSize
    ) {
        parent::__construct($reader, $storage);
        $this->batchSize = $batchSize ?? self::DEFAULT_BATCH_SIZE;
    }

    public function monitor(string $logFile, ?string $logFormat = null): int
    {
        if (!$this->reader->open($logFile)) {
            throw new \Exception(
                sprintf('Can not get access to log file %s', $logFile)
            );
        }

        $counter = 0;
        while ($items = $this->reader->chunk($this->batchSize)) {
            $this->storage->saveBatch($items);
            $counter += count($items);
        }

        return $counter;
    }
}