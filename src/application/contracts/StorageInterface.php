<?php

namespace aigletter\logging\application\contracts;

use aigletter\logging\application\dto\LogDto;

interface StorageInterface
{
    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return LogDto[]
     */
    public function findByStartDateAndFinishDate(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): array;

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return int
     */
    public function countByStartDateAndFinishDate(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): int;

    /**
     * @param LogDto[] $items
     * @return void
     */
    public function saveBatch(array $items): void;

    /**
     * @param LogDto $item
     * @return void
     */
    public function saveItem(LogDto $item): void;
}