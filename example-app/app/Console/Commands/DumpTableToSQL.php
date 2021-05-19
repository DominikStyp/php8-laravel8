<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DumpTableToSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump-table-to-sql {table : table to dump}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps table structure and data to SQL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $table = $this->argument('table');

        $targetFile = "database/schema/{$table}.sql";

        if(!is_dir($targetDir = dirname($targetFile))){
            mkdir($targetDir, 0777, true);
        }

        $this->line("Dumping table {$table}");

        $passwordPart = empty(config('database.connections.mysql.password')) ? '' : '-p"${:PASSWORD}"';
        $command = 'mysqldump -u "${:USERNAME}" -h "${:DB_HOST}" '.$passwordPart.' "${:DB_NAME}" "${:TABLE}" > "${:TARGET_FILE}"';

        // we use cross-platform process factory
        // @see: https://symfony.com/doc/current/components/process.html
        $process = Process::fromShellCommandline($command, null, [
            'USERNAME' => config('database.connections.mysql.username'),
            'PASSWORD' => config('database.connections.mysql.password'),
            'DB_HOST' => config('database.connections.mysql.host'),
            'DB_NAME' => config('database.connections.mysql.database'),
            'TABLE' => $table,
            'TARGET_FILE' => $targetFile
        ]);

        $this->line( json_encode($process->getEnv(), JSON_PRETTY_PRINT) );
        $this->line( $process->getCommandLine() );

        $process->run();

        // if something is wrong we throw an exception
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->line($process->getOutput());

        $this->line("table dumped to: $targetFile");

        return 0;
    }
}
