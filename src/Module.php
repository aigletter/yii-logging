<?php

namespace aigletter\logging;

use aigletter\logging\components\LoggingService;
use aigletter\logging\contracts\FileInterface;
use aigletter\logging\contracts\LoggingServiceInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\contracts\ReaderInterface;
use aigletter\logging\contracts\StorageInterface;
use aigletter\logging\implementations\File;
use aigletter\logging\implementations\NginxParser;
use aigletter\logging\implementations\Reader;
use aigletter\logging\implementations\Storage;
use Yii;
use yii\console\Application;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public string $db;

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        $config = require dirname(__DIR__) . '/config/config.php';
        $config['params'] = $this->mergeParams($config['params'], $this->params);
        $config['controllerMap'] = ArrayHelper::merge($config['controllerMap'], $this->controllerMap);

        if (empty($this->db)) {
            $this->db = $config['db'];
        }
        unset($config['db']);

        Yii::configure($this, $config);

        $this->setDefinitions();

        if (Yii::$app instanceof Application) {
            Yii::setAlias('@aigletter/logging/commands', __DIR__ . '/commands');
            Yii::setAlias('@aigletter/logging/migrations', dirname(__DIR__) . '/migrations');
            $this->controllerNamespace = 'aigletter\logging\commands';
        }
    }

    /**
     * @param $config
     * @param $custom
     * @return mixed
     */
    private function mergeParams($config, $custom)
    {
        foreach ($config as $key => $value) {
            if (isset($custom[$key])) {
                $config[$key] = $custom[$key];
            }
        }

        return $config;
    }

    /**
     * @return void
     */
    private function setDefinitions()
    {
        Yii::$container->setDefinitions([
            LoggingService::class => [
                ['class' => LoggingService::class],
                [
                    'defaultLogFile' => $this->params['defaultLogFile'],
                    'processMode' => $this->params['processMode'],
                    'batchSize' => $this->params['batchSize'],
                ]
            ],
            LoggingServiceInterface::class => [
                'class' => LoggingService::class,
            ],
            ParserInterface::class => [
                'class' => NginxParser::class,
                //'logFormat' => $this->params['logFormat'],
            ],
            StorageInterface::class => [
                'class' => Storage::class,
            ],
            ReaderInterface::class => [
                'class' => Reader::class,
            ],
            FileInterface::class => [
                'class' => File::class,
            ],
            NginxParser::class => [
                ['class' => NginxParser::class],
                ['logFormat' => $this->params['logFormat']],
            ],
            Storage::class => [
                ['class' => Storage::class],
                ['modelClass' => $this->params['modelClass'],]
            ],
        ]);
    }

    /**
     * @return Connection
     * @throws \yii\base\InvalidConfigException
     */
    public function getDb(): Connection
    {
        return \Yii::$app->get($this->db);
    }
}