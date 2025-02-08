<?php

namespace aigletter\logging\application;

use aigletter\logging\application\contracts\MonitorInterface;
use aigletter\logging\application\contracts\LoggingServiceInterface;
use aigletter\logging\application\contracts\StorageInterface;
use aigletter\logging\application\dto\LogDto;

class LoggingService implements LoggingServiceInterface
{
    /**
     * @var StorageInterface
     */
    private StorageInterface $storage;

    private MonitorInterface $handler;

    /**
     * @var string
     */
    private string $defaultLogFile;

    /**
     * @param StorageInterface $storage
     * @param MonitorInterface $handler
     * @param string $defaultLogFile
     */
    public function __construct(
        StorageInterface $storage,
        MonitorInterface $handler,
        string $defaultLogFile,
    ) {
        $this->storage = $storage;
        $this->handler = $handler;
        $this->defaultLogFile = $defaultLogFile;
    }

    /**
     * @param string|null $logFile
     * @param string|null $logFormat
     * @return int
     * @throws \yii\db\Exception
     */
    public function monitor(string $logFile = null, ?string $logFormat = null): int
    {
        $logFile = $logFile ?? $this->defaultLogFile;

        return $this->handler->monitor($logFile, $logFormat);
    }

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return array|LogDto[]
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
}