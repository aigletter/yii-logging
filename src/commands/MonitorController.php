<?php

namespace aigletter\logging\commands;

use aigletter\logging\contracts\FileReaderInterface;
use aigletter\logging\contracts\LoggingInterface;
use aigletter\logging\Module;
use yii\console\Controller;
use yii\console\ExitCode;

class MonitorController extends Controller
{
    public $logFile;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['logFile']
        );
    }

    public function actionIndex(LoggingInterface $logging)
    {
        try {
            $result = $logging->monitor($this->logFile);
            $this->stdout($result . ' items was added successfully');
        } catch (\Throwable $throwable) {
            $this->stderr('Error: ' . $throwable->getMessage() . PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    public function actionFind($startDate, $finishDate, LoggingInterface $logging)
    {
        try {
            $startDate = new \DateTime($startDate);
            $finishDate = new \DateTime($finishDate);

            $items = $logging->findByDates($startDate, $finishDate);

            foreach ($items as $item) {
                $this->stdout($item->origin . PHP_EOL);
            }
        } catch (\Throwable $throwable) {
            $this->stderr('Error: ' . $throwable->getMessage() . PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    public function actionCount($startDate, $finishDate, LoggingInterface $logging)
    {
        try {
            $startDate = new \DateTime($startDate);
            $finishDate = new \DateTime($finishDate);

            $count = $logging->countByDates($startDate, $finishDate);

            $this->stdout('Count items: ' . $count . PHP_EOL);
        } catch (\Throwable $throwable) {
            $this->stderr('Error: ' . $throwable->getMessage() . PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }
}