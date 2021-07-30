<?php

namespace App\Commands;

class NginxLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'nginx:logs {type=error : The log type}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest Nginx log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->argument('type');

        abort_if(! in_array($type, ['error', 'access']), 1, 'Log type must be either "error" or "access".');

        $this->showLogs('nginx_'.$type);
    }
}
