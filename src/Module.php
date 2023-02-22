<?php

namespace aigletter\logging;

use aigletter\logging\components\Logging;
use aigletter\logging\contracts\LoggingInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\implementations\ParserAdapter;
use Symfony\Component\Dotenv\Dotenv;
use Yii;
use yii\console\Application;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class Module extends \yii\base\Module
{
    public $db;

    public function init()
    {
        parent::init();

        $config = require dirname(__DIR__) . '/config/config.php';
        $config['params'] = ArrayHelper::merge($config['params'], $this->params);
        $config['controllerMap'] = ArrayHelper::merge($config['controllerMap'], $this->controllerMap);
        
        Yii::configure($this, $config);

        $this->setDefinitions();
        
        //$this->db = Yii::$app->get($this->params['db']);

        if (Yii::$app instanceof Application) {
            Yii::setAlias('@aigletter/logging/commands', __DIR__ . '/commands');
            Yii::setAlias('@aigletter/logging/migrations', dirname(__DIR__) . '/migrations');
            $this->controllerNamespace = 'aigletter\logging\commands';
        }
    }

    protected function setDefinitions()
    {
        Yii::$container->setDefinitions([
            LoggingInterface::class => [
                'class' => Logging::class,
                /*'parser' => Instance::of(ParserInterface::class),
                'processMode' => $this->params['processMode'],
                'batchSize' => $this->params['batchSize'],
                'defaultLogFile' => $this->params['defaultLogFile'],*/
            ],
            ParserInterface::class => [
                'class' => ParserAdapter::class,
                //'logFormat' => $this->params['logFormat'],
            ],
            Logging::class => [
                ['class' => Logging::class],
                [
                    'parser' => Instance::of(ParserInterface::class),
                    'modelClass' => $this->params['modelClass'],
                    'processMode' => $this->params['processMode'],
                    'batchSize' => $this->params['batchSize'],
                    'defaultLogFile' => $this->params['defaultLogFile'],
                ]
            ],
            ParserAdapter::class => [
                ['class' => ParserAdapter::class],
                ['logFormat' => $this->params['logFormat']],
            ]
        ]);
    }
    
    public function getDb(): Connection
    {
        return \Yii::$app->get($this->db);
    }
}