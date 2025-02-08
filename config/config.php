<?php

use aigletter\logging\commands\MigrateController;
use aigletter\logging\components\LoggingService;
use aigletter\logging\contracts\LoggingServiceInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\implementations\NginxParser;
use aigletter\logging\models\Log;

return [
    'components' => [
        LoggingServiceInterface::class => [
            'class' => LoggingService::class,
        ],
        ParserInterface::class => [
            'class' => NginxParser::class,
        ]
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'aigletter\logging\migrations',
            ],
        ],
    ],
    'db' => 'db',
    'params' => [
        'defaultLogFile' => getenv('DEFAULT_LOG_FILE') ?: '/var/log/nginx/access.log',
        // Default log format is "combined"
        'logFormat' => '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"',
        'processMode' => LoggingService::PROCESS_MODE_BATCH,
        'batchSize' => 5,
        'modelClass' => Log::class,
    ]
];