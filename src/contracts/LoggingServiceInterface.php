<?php

namespace aigletter\logging\contracts;

use aigletter\logging\models\Log;

interface LoggingServiceInterface
{
    /**
     * @param string|null $logFile
     * @return int
     */
    public function monitor(string $logFile = null): int;

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return Log[]
     */
    public function findByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): array;

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return int
     */
    public function countByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): int;
}