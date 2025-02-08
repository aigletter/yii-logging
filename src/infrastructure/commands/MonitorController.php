<?php

namespace aigletter\logging\infrastructure\commands;

use aigletter\logging\application\contracts\LoggingServiceInterface;
use yii\console\Controller;
use yii\console\ExitCode;

class MonitorController extends Controller
{
    /**
     * @var string
     */
    public $logFile;

    /**
     * @param $actionID
     * @return string[]
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['logFile']
        );
    }

    /**
     * @param LoggingServiceInterface $logging
     * @return int
     */
    public function actionIndex(LoggingServiceInterface $logging)
    {
        try {
            $result = $logging->monitor($this->logFile);
            if ($result) {
                $this->stdout($result . ' items was added successfully' . PHP_EOL);
            } else {
                $this->stdout('New items were not found' . PHP_EOL);
            }
        } catch (\Throwable $throwable) {
            $this->stderr('Error: ' . $throwable->getMessage() . PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    /**
     * @param $startDate
     * @param $finishDate
     * @param LoggingServiceInterface $logging
     * @return int
     */
    public function actionFind($startDate, $finishDate, LoggingServiceInterface $logging)
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

    /**
     * @param $startDate
     * @param $finishDate
     * @param LoggingServiceInterface $logging
     * @return int
     */
    public function actionCount($startDate, $finishDate, LoggingServiceInterface $logging)
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