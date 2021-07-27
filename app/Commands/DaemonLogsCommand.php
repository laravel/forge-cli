<?php

namespace App\Commands;

class DaemonLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:logs {daemon? : The daemon ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest daemon log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $daemonId = $this->askForDaemon('Which daemon would you like to retrieve the logs from');

        $this->step('Retrieving the latest daemon logs');

        $daemon = $this->forge->daemon($this->currentServer()->id, $daemonId);

        $this->showDaemonLogs($daemon->id, $daemon->user);
    }
}
