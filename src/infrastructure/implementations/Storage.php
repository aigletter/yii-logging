<?php

namespace aigletter\logging\infrastructure\implementations;

use aigletter\logging\application\contracts\StorageInterface;
use aigletter\logging\application\dto\LogDto;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class Storage implements StorageInterface
{
    /**
     * @var string
     */
    private string $modelClass;

    /**
     * @param string $modelClass
     */
    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return array
     */
    public function findByStartDateAndFinishDate(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): array
    {
        return $this->getQuery()->select('origin')->where([
            'between',
            'timeLocal',
            $startDate->format('Y-m-d H:i:s'),
            $finishDate->format('Y-m-d H:i:s')
        ])->all();
    }

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $finishDate
     * @return int
     */
    public function countByStartDateAndFinishDate(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): int
    {
        return $this->getQuery()->select('origin')->where([
            'between',
            'timeLocal',
            $startDate->format('Y-m-d H:i:s'),
            $finishDate->format('Y-m-d H:i:s')
        ])->count();
    }

    /**
     * @param array $items
     * @return void
     */
    public function saveBatch(array $items): void
    {
        if ($items = $this->filterExistingItems($items)) {
            // TODO Make dto properties/table columns mapping
            $values = array_map(function (LogDto $dto) {
                return (array) $dto;
            }, $items);
            $keys = array_keys($values[0]);

            \Yii::$app->getModule('logging')?->getDb()
                ->createCommand()
                ->batchInsert($this->getTableName(), $keys, $values)
                ->execute();
        }
    }

    /**
     * @param LogDto $item
     * @return void
     * @throws \yii\db\Exception
     */
    public function saveItem(LogDto $item): void
    {
        if (!$this->getQuery()->select('id')->where(['id' => $item->id])->exists()) {
            $entity = $this->newActiveRecord();
            $entity->setAttributes((array) $item, false);

            $entity->save();
        }
    }

    /**
     * @param array $items
     * @return array
     */
    private function filterExistingItems(array $items)
    {
        $existingIds = array_column(
            $this->getQuery()->select('id')->where(['id' => array_column($items, 'id')])->all(),
            'id'
        );

        return array_filter($items, function (LogDto $item) use ($existingIds) {
            return !in_array($item->id, $existingIds);
        });
    }

    /**
     * @return ActiveQuery
     */
    private function getQuery(): ActiveQuery
    {
        return call_user_func([$this->modelClass, 'find']);
    }

    /**
     * @return string
     */
    private function getTableName(): string
    {
        return call_user_func([$this->modelClass, 'tableName']);
    }

    /**
     * @return ActiveRecord
     */
    private function newActiveRecord(): ActiveRecord
    {
        return new ($this->modelClass)();
    }
}