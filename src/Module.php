<?php

namespace aigletter\logging;

use aigletter\logging\commands\MonitorController;
use aigletter\logging\components\Logging;
use aigletter\logging\contracts\LoggingInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\implementations\ParserAdapter;
use Yii;
use yii\console\Application;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $config = require dirname(__DIR__) . '/config/config.php';
        $config['params'] = ArrayHelper::merge($config['params'], $this->params);

        Yii::configure($this, $config);

        Yii::setAlias('@aigletter/logging/commands', __DIR__ . '/commands');

        Yii::$container->setDefinitions([
            Logging::class => [
                ['class' => Logging::class],
                [
                    Instance::of(ParserInterface::class),
                    $this->params['processType'],
                    $this->params['batchSize'],
                    $this->params['defaultLogFile'],
                    $this->params['logFormat']
                ]
            ],
            //MonitorController::class => [
                //['class' => MonitorController::class],
                //[Instance::of(LoggingInterface::class)]
            //],
            ParserInterface::class => [
                ['class' => ParserAdapter::class],
                [$this->params['logFormat']]
            ]
        ]);

        /*Yii::$container->setDefinitions([
            FileReaderInterface::class => [
                ['class' => FileReader::class],
                [$this->params['logfile'] ?? realpath('/var/log/nginx/access.log')]
            ],
            LoggingInterface::class => [
                ['class' => Logging::class],
                [\yii\di\Instance::of(FileReaderInterface::class)]
            ],
            MonitorController::class => [
                ['class' => MonitorController::class],
                [Instance::of(LoggingInterface::class)]
            ]
        ]);*/

        if (Yii::$app instanceof Application) {
            $this->controllerNamespace = 'aigletter\logging\commands';
        }
    }
}