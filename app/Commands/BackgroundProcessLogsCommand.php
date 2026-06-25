<?php

namespace App\Commands;

use function Laravel\Prompts\spin;

class BackgroundProcessLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'background-process:logs {backgroundProcess? : The background process ID}
                                                    {--f|follow : Monitor the log changes in realtime}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest background process log messages';

    /**
     * The aliases of the command.
     *
     * @var array
     */
    protected $aliases = [
        'daemon:logs',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $organization = $this->currentOrganization();
        $server = $this->currentServer();
        $processId = $this->askForBackgroundProcess('Which background process would you like to retrieve the logs from');

        $process = spin(
            fn () => $this->forge->backgroundProcess($organization, $server->id, (int) $processId),
            'Retrieving background process',
        );

        if ($this->option('follow')) {
            $this->showBackgroundProcessLogs($process->id, $process->user, true);

            return;
        }

        $logs = spin(
            fn () => $this->forge->backgroundProcessLog($organization, $server->id, $process->id),
            'Retrieving background process logs',
        );

        abort_if(empty($logs), 1, 'The requested logs could not be found or they are empty.');

        $this->newLine();

        $this->displayLogs($logs);

        $this->newLine();
    }
}
