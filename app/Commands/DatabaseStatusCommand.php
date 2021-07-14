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

        if (is_null($id = $this->option('id'))) {
            $name = $this->choice(
                'Which database would you like to know the current status?',
                ($databases = collect($this->forge->databases(
                    $serverId,
                )))->mapWithKeys(function ($database) {
                    return [$database->id => $database->name];
                })->all()
            );

            $id = $databases->where('name', $name)->first()->id;
        }

        $database = $this->forge->database($serverId, $id);

        $this->info(
            'The database <comment>['.$database->name.']</comment> is <comment>['.$database->status.']</comment>.'
        );
    }
}
