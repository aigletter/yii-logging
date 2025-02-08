<?php

namespace aigletter\logging\contracts;

use aigletter\logging\dto\LogDto;

interface StorageInterface
{
    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return array
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