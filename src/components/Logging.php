<?php

namespace aigletter\logging\components;

use aigletter\logging\contracts\LoggingInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\dto\LogDto;
use aigletter\logging\implementations\FileReader;
use aigletter\logging\implementations\FileReaderFilter;
use aigletter\logging\models\Log;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

class Logging extends Component implements LoggingInterface
{
    public const DEFAULT_LOG_FILE = '/var/log/nginx/access.log';

    public const DEFAULT_BATCH_SIZE = 1000;

    public const PROCESS_MODE_SINGLE = 'single';

    public const PROCESS_MODE_BATCH = 'batch';

    /**
     * @var string
     */
    protected $defaultLogFile;

    protected $modelClass;

    protected $processMode;

    protected $batchSize;

    protected $parser;

    public function __construct(
        ParserInterface $parser,
        string $modelClass,
        ?string $processMode = self::PROCESS_MODE_SINGLE,
        ?string $batchSize = self::DEFAULT_BATCH_SIZE,
        ?string $defaultLogFile = self::DEFAULT_LOG_FILE,
        $config = []
    ) {
        $this->parser = $parser;
        $this->modelClass  = $modelClass;
        $this->processMode = $processMode;
        $this->batchSize = $batchSize;
        $this->defaultLogFile = $defaultLogFile;

        parent::__construct($config);
    }

    /**
     * @param string|null $logFile
     * @param string|null $logFormat
     * @return int
     * @throws \yii\db\Exception
     */
    public function monitor(string $logFile = null, string $logFormat = null): int
    {
        $fileReader = new FileReaderFilter(
            new FileReader($logFile ?? $this->defaultLogFile)
        );

        if ($this->processMode === self::PROCESS_MODE_BATCH) {
            return $this->processBatch($fileReader);
        }

        return $this->processSingle($fileReader);
    }

    public function findByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): array
    {
        /** @var ActiveQuery $query */
        $query = $this->modelClass::find();
        return $query->select('origin')->where([
            'between',
            'timeLocal',
            $startDate->format('Y-m-d H:i:s'),
            $finishDate->format('Y-m-d H:i:s')
        ])->all();
    }

    public function countByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): int
    {
        /** @var ActiveQuery $query */
        $query = $this->modelClass::find();
        return $query->select('origin')->where([
            'between',
            'timeLocal',
            $startDate->format('Y-m-d H:i:s'),
            $finishDate->format('Y-m-d H:i:s')
        ])->count();
    }

    /**
     * @param \Iterator $lines
     * @return int
     * @throws \yii\db\Exception
     */
    protected function processBatch(\Iterator $lines): int
    {
        $items = [];

        foreach ($lines as $line) {
            $hash = md5($line);
            $item = $this->parser->parse($line);
            $item->origin = trim($line);
            $items[$hash] = $item;
        }

        $existingIds = array_column(
            Log::find()->select('id')->where(['id' => array_keys($items)])->all(),
            'id'
        );

        /*$items = array_diff_key($allItems, array_fill_keys($existingIds, null));*/
        $items = array_filter(
            $items,
            function ($id) use ($existingIds) {
                return !in_array($id, $existingIds);
            },
            ARRAY_FILTER_USE_KEY
        );

        foreach (array_chunk($items, $this->batchSize, true) as $chunk) {
            $this->addItems(...$chunk);
        }

        return count($items);
    }

    protected function processSingle(\Iterator $lines): int
    {
        $counter = 0;
        foreach ($lines as $line) {
            $hash = md5($line);
            if (!Log::find()->where(['id' => $hash])->exists()) {
                $item = $this->parser->parse($line);
                $item->origin = trim($line);
                //$item->timeLocal = 'toDateTime(' . $item->timeLocal . ')';
                $this->addItem($hash, $item);
                $counter++;
            }
        }

        return $counter;
    }

    protected function addItem(string $id, LogDto $dto) {
        /** @var ActiveRecord $entity */
        $entity = new ($this->modelClass)();
        $entity->id = $id;
        $entity->setAttributes((array) $dto, false);

        $entity->save();
    }

    /**
     * associative array with string keys including identifiers like
     * [
     *      '37ae39aeb6f6029f76a5f3a4a0530378' => [
     *          'timeLocal' => '2023-02-18 23:44:16',
     *          'request' => 'GET / HTTP/1.1'
     *      ]
     * ]
     * @param LogDto ...$items
     * @return void
     * @throws \yii\db\Exception
     */
    protected function addItems(LogDto ...$items)
    {
        $keys = [];
        $values = array_map(
            function (string $id, LogDto $dto) use (&$keys) {
                $item = array_merge(['id' => $id], (array) $dto);
                if (empty($keys)) {
                    $keys = array_keys($item);
                }
                return array_values($item);
            },
            array_keys($items),
            array_values($items)
        );


        \Yii::$app->db->createCommand()->batchInsert(Log::tableName(), $keys, $values)->execute();
    }
}