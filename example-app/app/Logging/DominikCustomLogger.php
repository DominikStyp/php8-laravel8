<?php


namespace App\Logging;


use Monolog\Logger;

class DominikCustomLogger {
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('dominik_custom');

        $logger->pushHandler(new DominikLogHandler());
        /**
         * WARNING! In stack driver processor is NEVER used
         */
        $logger->pushProcessor(new DominikLogProcessor());

        return $logger;
    }
}
