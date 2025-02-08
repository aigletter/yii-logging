<?php

namespace aigletter\logging\application\monitors;

class SingleMonitor extends AbstractMonitor
{
    public function monitor(string $logFile, ?string $logFormat = null): int
    {
        if (!$this->reader->open($logFile)) {
            throw new \Exception(
                sprintf('Can not get access to log file %s', $logFile)
            );
        }

        $counter = 0;
        while ($item = $this->reader->read()) {
            $this->storage->saveItem($item);
            $counter++;
        }

        return $counter;
    }
}