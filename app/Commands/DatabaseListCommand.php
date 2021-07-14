<?php

namespace App\Commands;

class DatabaseListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the databases';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->table([
            'ID', 'Name', 'Status',
        ], collect($this->forge->databases(
            $this->currentServer()->id
        ))->map(function ($database) {
            return [
                $database->id,
                $database->name,
                $database->status,
            ];
        })->all());
    }
}
