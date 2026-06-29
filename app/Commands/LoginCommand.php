<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\password;

class LoginCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'login {--token= : Forge API token}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Authenticate with Laravel Forge';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $token = $this->option('token');

        if ($token === null) {
            $token = password(
                label: 'Please enter your Forge API token',
                required: true,
            );
        }

        $this->config->set('token', $token);

        $email = $this->forge->user()->email;

        $organizations = collect($this->forge->organizations());

        if ($organizations->count() === 1) {
            $this->config->set('organization', $organizations->first()->slug);
        }

        info("Authenticated successfully as $email");
    }
}
