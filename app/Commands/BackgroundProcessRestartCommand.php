<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

class BackgroundProcessRestartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'background-process:restart {backgroundProcess? : The background process ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart a background process';

    /**
     * The aliases of the command.
     *
     * @var array<string>
     */
    protected $aliases = [
        'daemon:restart',
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
        $processId = $this->askForBackgroundProcess('Which background process would you like to restart');

        $process = spin(
            fn () => $this->forge->backgroundProcess($organization, $server->id, (int) $processId),
            'Retrieving background process',
        );

        abort_unless($process->status == 'installed', 1, 'This background process is not installed or running.');

        spin(
            fn () => $this->forge->performBackgroundProcessAction(
                $organization,
                $server->id,
                $process->id,
                ['action' => 'restart'],
            ),
            'Restarting background process',
        );

        info('Background process restart initiated successfully.');
    }
}
