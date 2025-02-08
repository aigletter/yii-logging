<?php

namespace aigletter\logging\application;

use aigletter\logging\application\contracts\LoggingServiceInterface;
use aigletter\logging\application\contracts\ReaderInterface;
use aigletter\logging\application\contracts\StorageInterface;

class LoggingService implements LoggingServiceInterface
{
    public const DEFAULT_BATCH_SIZE = 100;

    public const PROCESS_MODE_SINGLE = 'single';

    public const PROCESS_MODE_BATCH = 'batch';

    /**
     * @var ReaderInterface
     */
    private ReaderInterface $reader;

    /**
     * @var StorageInterface
     */
    protected StorageInterface $storage;

    /**
     * @var string
     */
    private string $defaultLogFile;

    /**
     * @var string|null
     */
    private string $processMode;

    /**
     * @var int|null
     */
    private int $batchSize;

    /**
     * @param ReaderInterface $reader
     * @param StorageInterface $storage
     * @param string $defaultLogFile
     * @param string $processMode
     * @param int $batchSize
     */
    public function __construct(
        ReaderInterface $reader,
        StorageInterface $storage,
        string $defaultLogFile,
        string $processMode = self::PROCESS_MODE_SINGLE,
        int $batchSize = self::DEFAULT_BATCH_SIZE,
    ) {
        $this->reader = $reader;
        $this->storage = $storage;
        $this->defaultLogFile = $defaultLogFile;

        if ($processMode) {
            $this->checkProcessMode($processMode);
        }
        $this->processMode = $processMode;
        $this->batchSize = $batchSize;
    }

    /**
     * @param string|null $logFile
     * @param string|null $logFormat
     * @return int
     * @throws \yii\db\Exception
     */
    public function monitor(?string $logFile = null, ?string $logFormat = null): int
    {
        $logFile = $logFile ?? $this->defaultLogFile;

        if (!$this->reader->open($logFile)) {
            throw new \Exception(
                sprintf('Can not get access to log file %s', $logFile)
            );
        }

        if ($this->processMode === self::PROCESS_MODE_BATCH) {
            return $this->processBatch();
        }

        return $this->processSingle();
    }

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return array|Log[]
     */
    public function findByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): array
    {
        return $this->storage->findByStartDateAndFinishDate($startDate, $finishDate);
    }

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return int
     */
    public function countByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): int
    {
        return $this->storage->countByStartDateAndFinishDate($startDate, $finishDate);
    }

    /**
     * @param string $processMode
     * @return void
     */
    private function checkProcessMode(string $processMode): void
    {
        if (!in_array($processMode, [self::PROCESS_MODE_SINGLE, self::PROCESS_MODE_BATCH])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument "processMode" must be %s or %s',
                    self::PROCESS_MODE_SINGLE,
                    self::PROCESS_MODE_BATCH,
                )
            );
        }
    }

    /**
     * @return int
     */
    protected function processBatch(): int
    {
        $counter = 0;
        while ($items = $this->reader->chunk($this->batchSize)) {
            $this->storage->saveBatch($items);
            $counter += count($items);
        }

        return $counter;
    }

    /**
     * @return int
     */
    protected function processSingle(): int
    {
        $counter = 0;
        while ($item = $this->reader->read()) {
            $this->storage->saveItem($item);
            $counter++;
        }

        return $counter;
    }
}