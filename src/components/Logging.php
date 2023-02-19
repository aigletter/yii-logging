<?php

namespace aigletter\logging\components;

use aigletter\logging\contracts\LoggingInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\dto\LogDto;
use aigletter\logging\implementations\FileReader;
use aigletter\logging\implementations\FileReaderFilter;
use aigletter\logging\models\Log;

class Logging implements LoggingInterface
{
    public const DEFAULT_LOG_FORMAT = '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"';

    public const DEFAULT_LOG_FILE = '/var/log/nginx/access.log';

    public const DEFAULT_BATCH_SIZE = 1000;

    public const PROCESS_TYPE_SINGLE = 'single';

    public const PROCESS_TYPE_BATCH = 'batch';

    /**
     * @var string
     */
    protected $defaultLogFile;

    protected $processType;

    protected $batchSize;

    /**
     * @var string
     */
    protected $defaultLogFormat;

    protected $parser;

    public function __construct(
        ParserInterface $parser,
        ?string $processType = self::PROCESS_TYPE_SINGLE,
        ?string $batchSize = self::DEFAULT_BATCH_SIZE,
        ?string $defaultLogFile = self::DEFAULT_LOG_FILE,
        ?string $defaultLogFormat = self::DEFAULT_LOG_FORMAT
    ) {
        $this->parser = $parser;
        $this->processType = $processType;
        $this->batchSize = $batchSize;
        $this->defaultLogFile = $defaultLogFile;
        $this->defaultLogFormat = $defaultLogFormat;
    }

    /**
     * 127.0.0.1 - - [17/Feb/2023:19:07:40 +0000] "GET / HTTP/1.1" 500 39 "-" "Mozilla/5.0 (X11; Linux x86_64) ..."
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

        if ($this->processType === self::PROCESS_TYPE_BATCH) {
            return $this->processBatch($fileReader);
        }

        return $this->processSingle($fileReader);
    }

    public function findByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): array
    {
        return Log::find()->select('origin')->where([
            'between',
            'timeLocal',
            $startDate->format('Y-m-d H:i:s'),
            $finishDate->format('Y-m-d H:i:s')
        ])->all();
    }

    public function countByDates(\DateTimeInterface $startDate, \DateTimeInterface $finishDate): int
    {
        return Log::find()->select('origin')->where([
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
                $this->addItem($hash, $item);
                $counter++;
            }
        }

        return $counter;
    }

    protected function addItem(string $id, LogDto $dto) {
        $entity = new Log();
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