<?php


namespace App\Logging;


class DominikLogProcessor {

    public function __invoke(array $record): array
    {
//        ob_start();
//        debug_print_backtrace();
//        $backtrace = ob_get_clean();

//        $record['extra']['backtrace'] = $backtrace;

        return $record;
    }
}
