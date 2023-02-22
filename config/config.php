<?php

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
            'class' => \aigletter\logging\commands\MigrateController::class,
            'migrationNamespaces' => [
                //'app\migrations',
                'aigletter\logging\migrations',
            ],
            //'migrationPath' => null, // allows to disable not namespaced migration completely
        ],
    ],

    'params' => [
        'defaultLogFile' => getenv('DEFAULT_LOG_FILE') ?: realpath('/var/log/nginx/access.log'),
        // Default log format is "combined"
        'logFormat' => getenv('LOG_FORMAT') ?: '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"',
        'processMode' => getenv('PROCESS_MODE') ?: Logging::PROCESS_MODE_SINGLE,
        'batchSize' => getenv('BATCH_SIZE') ?: 5,
        'modelClass' => Log::class,
        'db' => 'db'
    ]
];