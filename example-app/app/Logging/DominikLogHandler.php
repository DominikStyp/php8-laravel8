<?php


namespace App\Logging;


use Illuminate\Support\Facades\Storage;
use Monolog\Handler\AbstractProcessingHandler;

class DominikLogHandler extends AbstractProcessingHandler {

    protected function write(array $record): void
    {
        /**
         * @see config/filesystems.php for the disk configuration
         */
        Storage::disk('logs')->append('dominik_log_handler.log', var_export($record, true));
    }
}
