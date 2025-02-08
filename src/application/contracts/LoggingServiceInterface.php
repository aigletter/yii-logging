<?php

namespace aigletter\logging\application\contracts;

use aigletter\logging\application\dto\LogDto;

interface LoggingServiceInterface extends MonitorInterface
{
    public const PROCESS_MODE_SINGLE = 'single';

    public const PROCESS_MODE_BATCH = 'batch';

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return LogDto[]
     */
    public function findByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): array;

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return int
     */
    public function countByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): int;
}