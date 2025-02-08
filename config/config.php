<?php

use aigletter\logging\infrastructure\commands\MigrateController;
use aigletter\logging\application\LoggingService;
use aigletter\logging\application\contracts\LoggingServiceInterface;
use aigletter\logging\application\contracts\ParserInterface;
use aigletter\logging\infrastructure\implementations\NginxParser;
use aigletter\logging\infrastructure\models\Log;

return [
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