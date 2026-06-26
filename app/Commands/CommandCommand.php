<?php

namespace App\Commands;

use Laravel\Forge\Resources\Command as SiteCommand;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class CommandCommand extends Command
{
    use Concerns\InteractsWithLogs;

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
        $siteId = (int) $this->askForSite('Which site would you like to run the command on');
        $organization = $this->currentOrganization();
        $server = $this->currentServer();

        $command = $this->option('command') ?? text('What command would you like to execute');

        $command = spin(function () use ($organization, $server, $siteId, $command) {
            $this->forge->createCommand($organization, $server->id, $siteId, [
                'command' => $command,
            ]);

            return collect($this->forge->commands($organization, $server->id, $siteId)->lazy())->first();
        }, 'Queuing command');

        $command = spin(function () use ($organization, $server, $siteId, $command) {
            while (in_array($command->status, ['waiting', 'running'])) {
                $this->time->sleep(1);

                /** @var SiteCommand $command */
                $command = $this->forge->command($organization, $server->id, $siteId, $command->id);
            }

            return $command;
        }, 'Running command');

        $output = $this->forge->commandOutput($organization, $server->id, $siteId, $command->id);

        $this->newLine();

        $this->displayLogs($output);

        $this->newLine();

        abort_if($command->status !== 'finished', 1, 'The command failed.');

        info('Command run successfully.');
    }
}
