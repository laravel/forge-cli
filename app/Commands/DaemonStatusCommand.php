<?php

namespace App\Commands;

class DaemonStatusCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:status {daemon? : The daemon ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of a daemon';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $daemonId = $this->argument('daemon') ?? $this->askForDaemon('Which daemon would you like to check the status of');

        $daemon = $this->forge->daemon($this->currentServer()->id, $daemonId);

        abort_if(is_null($daemon), 1, 'The daemon could not be found.');

        $this->step(['Daemon Status: %s', $daemon->command]);

        $this->table([], [
            ['ID', $daemon->id],
            ['Command', '<fg=blue>'.$daemon->command.'</>'],
            ['Status', $this->formatStatus($daemon->status)],
            ['User', $daemon->user],
            ['Directory', $daemon->directory ?: '-'],
            ['Created', $daemon->createdAt],
        ]);
    }

    /**
     * Format the daemon status with color.
     *
     * @param  string  $status
     * @return string
     */
    protected function formatStatus($status)
    {
        return match ($status) {
            'installed' => '<fg=green>'.ucfirst($status).'</>',
            'installing' => '<fg=yellow>'.ucfirst($status).'</>',
            default => '<fg=red>'.ucfirst($status).'</>',
        };
    }
}
