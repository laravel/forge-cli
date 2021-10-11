<?php

namespace App\Commands;

use App\Support\Boolean;

class SshPrivateIpAccessCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh:private-ip-access {enable? : Enable SSH access over private IPs}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Configure Forge CLI to use private server IPs for SSH access';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $enablePrivateIPAccess = $this->argument('enable');
        if (is_null($enablePrivateIPAccess)) {
            $enablePrivateIPAccess = $this->confirm('Do you wish to enable SSH access over your servers private IP?');
        }
        abort_unless($this->ensureEnableIsBoolean($enablePrivateIPAccess), 1, 'Enable has to be a boolean value');

        $this->step('Configuring SSH private IP access');

        $this->config->set('ssh_private_ip_access', Boolean::fromValue($enablePrivateIPAccess));

        $this->successfulStep('SSH private IP access is configured');
    }

    /**
     * @param mixed $enable
     * @return bool
     */
    protected function ensureEnableIsBoolean($enable): bool
    {
        return null !== Boolean::fromValue($enable);
    }
}
