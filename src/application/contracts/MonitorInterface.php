<?php

namespace aigletter\logging\application\contracts;

interface MonitorInterface
{
    public function monitor(string $logFile, ?string $logFormat = null): int;
}