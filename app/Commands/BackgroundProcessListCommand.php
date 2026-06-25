<?php

namespace App\Commands;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class BackgroundProcessListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'background-process:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the background processes';

    /**
     * The aliases of the command.
     *
     * @var array
     */
    protected $aliases = [
        'daemon:list',
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

        $processes = spin(
            fn () => collect($this->forge->backgroundProcesses($organization, $server->id)->lazy()),
            'Retrieving background processes',
        );

        if ($processes->isEmpty()) {
            warning('No background processes found.');

            return;
        }

        table([
            'ID', 'Command', 'Status',
        ], $processes->map(function ($process) {
            return [
                $process->id,
                $process->command,
                ucfirst($process->status),
            ];
        })->all());
    }
}
