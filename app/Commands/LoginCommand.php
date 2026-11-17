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

        [$email, $organizations] = $this->forge->usingToken($token, fn () => [
            $this->forge->user()->email,
            collect($this->forge->organizations()),
        ]);

        $tokens = $this->config->get('tokens', []);

        // Configs written before per-organization tokens only have a single "token",
        // so pin it to the current organization before it gets replaced...
        if (! is_null($current = $this->config->get('organization')) && ! is_null($previous = $this->config->get('token'))) {
            $tokens[$current] ??= $previous;
        }

        $this->config->set('token', $token);
        $this->config->set('tokens', $organizations->mapWithKeys(
            fn ($organization) => [$organization->slug => $token],
        )->all() + $tokens);

        if ($organizations->count() === 1) {
            $this->config->set('organization', $organizations->first()->slug);
        }

        info("Authenticated successfully as $email");
    }
}
