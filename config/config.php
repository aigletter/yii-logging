<?php

use aigletter\logging\commands\MigrateController;
use aigletter\logging\components\Logging;
use aigletter\logging\contracts\LoggingInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\implementations\ParserAdapter;
use aigletter\logging\models\Log;

return [
    'components' => [
        LoggingInterface::class => [
            'class' => Logging::class,
        ],
        ParserInterface::class => [
            'class' => ParserAdapter::class,
        ]
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                //'app\migrations',
                'aigletter\logging\migrations',
            ],
            //'migrationPath' => null, // allows to disable not namespaced migration completely
        ],
    ],
    'db' => 'db',
    'params' => [
        'defaultLogFile' => '/var/log/nginx/access.log',
        // Default log format is "combined"
        'logFormat' => '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"',
        'processMode' => Logging::PROCESS_MODE_SINGLE,
        'batchSize' => 5,
        'modelClass' => Log::class,
    ]
];