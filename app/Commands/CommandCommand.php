<?php

namespace App\Commands;

class CommandCommand extends Command
{
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

        $siteId = $this->askForId('Which site would you like to deploy?', $sites);

        $command = $this->option('command') ?? $this->ask('What command would you like to execute?');

        /// $command = $this->forge->listCommandHistory($this->);

        $command = $this->forge->executeSiteCommand($server->id, $siteId, [
            'command' => $command,
        ]);

        do {
            $command = $this->forge->getSiteCommand($server->id, $siteId, $command->id);
            dd($command);
            dd($command->status);
            sleep(1);
        } while ($status == 'waiting');
    }

    /**
     * Restarts MySQL database service.
     *
     * @param  string|int $serverId
     * @return bool
     */
    public function restartMysql($serverId)
    {
        if ($restarting = $this->confirm('While the <comment>[MySQL]</comment> service restarts, the database may become unavailable. Wish to proceed?')) {
            $this->forge->rebootMysql($serverId);
        }

        return $restarting;
    }

    /**
     * Restarts PostgreSQL database service.
     *
     * @param  string|int $serverId
     * @return bool
     */
    public function restartPostgres($serverId)
    {
        if ($restarting = $this->confirm('While the <comment>[PostgreSQL]</comment> service restarts, the database may become unavailable. Wish to proceed?')) {
            $this->forge->rebootPostgres($serverId);
        }

        return $restarting;
    }
}
