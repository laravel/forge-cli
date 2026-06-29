<?php

namespace App\Commands;

use Throwable;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class SshConfigureCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh:configure
        {server? : The server name}
        {--key= : The path to the public key}
        {--name= : The key name on Forge}
        {--user= : The server username}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Configure SSH key based secure authentication';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $serverId = $this->askForServer('Which server would you like to configure the SSH key based secure authentication');

        if ($this->currentServer()->id != $serverId) {
            $this->call('server:switch', [
                'server' => $serverId,
            ]);
        }

        $key = $this->getKey();

        $privateKey = $this->ensureKeyExists($this->getKeyName($key), $key, $this->getServerUsername());

        $this->remote->resolvePrivateKeyUsing(fn () => $privateKey);

        spin(function () {
            for ($attempt = 1; $attempt <= 12; $attempt++) {
                try {
                    $this->remote->ensureSshIsConfigured();

                    return;
                } catch (Throwable $e) {
                    if ($attempt === 12) {
                        throw $e;
                    }

                    $this->time->sleep(5);
                }
            }
        }, 'Waiting for SSH key to be configured');

        info('SSH key based secure authentication configured successfully');
    }

    /**
     * Ensures the given SSH Key exists.
     *
     * @param  string  $name
     * @param  string|null  $key
     * @param  string  $username
     * @return string
     */
    protected function ensureKeyExists($name, $key = null, $username = 'forge')
    {
        $server = $this->currentServer();

        if ($key) {
            [$localName, $key] = $this->keys->get($key);
        } else {
            [$localName, $key] = $this->keys->create($name);

            info("Creating key $localName");
        }

        info("Adding key $localName with the name $name to server {$server->name}");

        $this->forge->createSshKey($this->currentOrganization(), $server->id, ['key' => trim($key), 'name' => $name, 'username' => $username]);

        return $this->keys->keysPath().'/'.basename($localName, '.pub');
    }

    /**
     * Gets the SSK Key "option".
     *
     * @return string|null
     */
    protected function getKey()
    {
        if (is_null($key = $this->option('key'))) {
            $localKeys = collect($this->keys->local());

            $choices = collect(['create' => 'Create new key'])->merge($localKeys->mapWithKeys(function ($key) {
                return [$key => 'Reuse '.str_replace($this->keys->keysPath().'/', '', $key)];
            }))->all();

            $choice = select('Which key would you like to use', $choices);

            if ($choice !== 'create') {
                $key = $choice;
            }
        }

        return $key;
    }

    /**
     * Gets the SSH Key name.
     *
     * @param  string|null  $key
     * @return string
     */
    protected function getKeyName($key)
    {
        $question = 'What should the SSH key be named';

        if ($key) {
            $question .= ' in Forge';
        }

        return $this->option('name') ?: text($question, default: get_current_user());
    }

    /**
     * Prompt for a "server user".
     *
     * @return string
     */
    protected function getServerUsername()
    {
        $question = 'What username should we use for the selected server';

        return $this->option('user') ?: text($question, default: 'forge');
    }
}
