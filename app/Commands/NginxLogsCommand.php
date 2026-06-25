<?php

namespace App\Commands;

use function Laravel\Prompts\info;

class NginxLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'nginx:logs {type=error : The log type}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest Nginx log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->argument('type');

        abort_if(! in_array($type, ['error', 'access']), 1, 'Log type must be "error" or "access".');

        info("Retrieving the latest {$type} logs");

        $logs = $this->forge->serverLog(
            $this->currentOrganization(),
            $this->currentServer()->id,
            'nginx-'.$type,
        );

        abort_if(empty($logs), 1, 'The requested logs could not be found or they are empty.');

        $this->newLine();

        $this->displayLogs($logs);

        $this->newLine();
    }
}
