<?php

namespace App\Commands;

class DatabaseStatusCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:status {--id= : The ID of the database}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of a database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $serverId = $this->currentServer()->id;

        $databaseId = $this->askForId(
            'Which database would you like to know the current status?',
            function () use ($serverId) {
                return $this->forge->databases($serverId);
            }
        );

        $database = $this->forge->database($serverId, $databaseId);

        $this->info(
            'The database <comment>['.$database->name.']</comment> is <comment>['.$database->status.']</comment>.'
        );
    }
}
