<?php

namespace App\Commands;

use App\Exceptions\LogicException;
use Illuminate\Support\Str;

class NginxLogsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'nginx:logs {--type=error}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest nginx log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        $type = $this->option('type');

        if (! in_array($type, ['error', 'access'])) {
            throw new LogicException('Logs type must be either "error" or "access".');
        }

        $logs = $this->forge->logs($server->id, 'nginx_'.$type);

        Str::of($logs->content)
            ->trim()
            ->explode("\n")
            ->each(function ($line) {
                $this->line($line);
            });
    }
}
