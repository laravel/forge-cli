<?php

namespace App\Commands;

class DaemonListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the daemons';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->step('Retrieving the list of daemons');

        $daemons = $this->forge->daemons(
            $this->currentServer()->id
        );

        $this->table([
            'ID', 'Command', 'Status',
        ], collect($daemons)->map(function ($daemon) {
            return [
                $daemon->id,
                '<fg=blue>'.$daemon->command.'</>',
                ucfirst($daemon->status),
            ];
        })->all());
    }
}
