<?php

namespace App\Commands;

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

        $this->callSilently('ssh:test', ['--key' => $privateKey]);

        $this->successfulStep('SSH key based secure authentication configured successfully');
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

            $this->step('Creating Key <comment>['.$localName.']</comment>');
        }

        $this->step('Adding Key <comment>['.$localName.']</comment>'.' With The Name <comment>['.$name.']</comment> To Server <comment>['.$server->name.']</comment>');

        $this->forge->createSSHKey($server->id, ['key' => $key, 'name' => $name, 'username' => $username], true);

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

            $choices = collect(['<comment>Create new key</comment>'])->merge($localKeys->map(function ($key) {
                return '<comment>Reuse</comment> '.str_replace($this->keys->keysPath().'/', '', $key);
            }))->values()->all();

            $choice = $this->choiceStep('Which key would you like to use', $choices);

            if ($choice > 0) {
                $key = $localKeys->get($choice - 1);
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

        return $this->option('name') ?: $this->askStep($question, get_current_user());
    }

    /**
     * Prompt for a "server user"
     *
     * @return string
     */
    protected function getServerUsername()
    {
        $question = 'What username should we use for the selected server';

        return $this->option('user') ?: $this->askStep($question, 'forge');
    }
}