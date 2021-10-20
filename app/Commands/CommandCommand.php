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
        {site? : The site name}
        {--command= : The command that should be executed}';

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
        $siteId = $this->askForSite('Which site would you like to run the command on');

        $command = $this->option('command') ?? $this->askStep('What command would you like to execute');

        $this->step('Queuing Command');

        $server = $this->currentServer();

        $command = $this->forge->executeSiteCommand($server->id, $siteId, [
            'command' => $command,
        ]);

        $this->step('Waiting For Command To Run');

        do {
            $this->time->sleep(1);

            /** @var \Laravel\Forge\Resources\SiteCommand $command */
            $command = collect($this->forge->getSiteCommand($server->id, $siteId, $command->id))->first();
        } while ($command->status == 'waiting');

        $this->step('Running');

        $eventId = $this->findEventId('Running Custom Command.');

        $username = $this->forge->site($server->id, $siteId)->username;

        $this->displayEventOutput($username, $eventId, function () use ($server, $siteId, &$command) {
            $command = collect($this->forge->getSiteCommand($server->id, $siteId, $command->id))->first();

            /** @var \Laravel\Forge\Resources\SiteCommand $command */
            return $command->status == 'running';
        });

        abort_if($command->status == 'failed', 1, 'The command failed.');

        $this->successfulStep('Command Run Successfully.');
    }
}
