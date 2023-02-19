<?php

use aigletter\logging\components\Logging;
use aigletter\logging\contracts\LoggingInterface;
use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\implementations\ParserAdapter;

return [
    'components' => [
        LoggingInterface::class => [
            'class' => Logging::class,
        ],
        ParserInterface::class => [
            'class' => ParserAdapter::class,
        ]

    ],
    'params' => [
        'defaultLogFile' => realpath('/var/log/nginx/access.log'),
        // Default log format is "combined"
        'logFormat' => '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"',
        'batchSize' => 5,
        'processType' => Logging::PROCESS_TYPE_SINGLE,
    ],
];