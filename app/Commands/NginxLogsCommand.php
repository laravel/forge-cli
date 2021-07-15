<?php

namespace App\Commands;

use App\Exceptions\LogicException;

class NginxLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

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

        $this->showLogs($server, 'nginx_'.$type);
    }
}
