<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

class BackgroundProcessStatusCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'background-process:status {backgroundProcess? : The background process ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of a background process';

    /**
     * The aliases of the command.
     *
     * @var array<string>
     */
    protected $aliases = [
        'daemon:status',
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
        $processId = $this->askForBackgroundProcess('Which background process would you like to check');

        $process = spin(
            fn () => $this->forge->backgroundProcess($organization, $server->id, (int) $processId),
            'Retrieving background process',
        );

        info("The background process is {$process->status}.");
    }
}
