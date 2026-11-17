<?php

namespace App\Commands;

use function Laravel\Prompts\info;

class LogoutCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'logout {--all : Remove the tokens of all organizations}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Logout from Laravel Forge';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->config->flush();

            info('Logged out successfully');

            return;
        }

        $token = $this->forge->token();

        $this->config->set('tokens', collect($this->config->get('tokens', []))
            ->reject(fn ($value) => $value === $token)
            ->all());

        if ($this->config->get('token') === $token) {
            $this->config->forget('token');
        }

        $this->config->forget('organization');
        $this->config->forget('server');

        if (empty($this->config->get('tokens')) && is_null($this->config->get('token'))) {
            $this->config->flush();
        }

        info('Logged out successfully');
    }
}
