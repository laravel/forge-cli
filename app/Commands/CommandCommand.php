<?php

namespace App\Commands;

class CommandCommand extends Command
{
    use Concerns\InteractsWithEvents;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'command
        {--id= : The ID of the site}
        {--command= : Execute a CLI command}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Execute a CLI command';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        $sites = function () {
            return $this->forge->sites($this->currentServer()->id);
        };

        $siteId = $this->askForId('Which site would you like to run the command on', $sites);

        $command = $this->option('command') ?? $this->ask('What command would you like to execute');

        $this->step('Queuing Command');

        $command = $this->forge->executeSiteCommand($server->id, $siteId, [
            'command' => $command,
        ]);

        $this->step('Waiting For Command To Run');

        do {
            $this->time->sleep(1);

            $command = collect($this->forge->getSiteCommand($server->id, $siteId, $command->id))->first();
        } while ($command->status == 'waiting');

        $this->step('Running');

        $eventId = $this->findEventId('Running Custom Command.');

        $this->displayEventOutput($eventId, function () use ($server, $siteId, &$command) {
            $command = collect($this->forge->getSiteCommand($server->id, $siteId, $command->id))->first();

            return $command->status == 'running';
        });

        abort_if($command->status == 'failed', 1, 'The command failed.');

        $this->successfulStep('Command Run Successfully.');
    }
}
