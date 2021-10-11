<?php
it('can store the ssh private ip access config value by parameter', function ($input, $output) {
    $this->artisan('ssh:private-ip-access ' . $input)
        ->assertExitCode(0)
        ->run();

    expect($this->config->get('ssh_private_ip_access'))
        ->toBe($output);

})->with([['true', true], ['false', false], ['0', false], ['1', true]]);

it('can store the ssh private ip access config value by user input', function ($input, $output) {
    $this->artisan('ssh:private-ip-access')
        ->assertExitCode(0)
        ->expectsConfirmation('Do you wish to enable SSH access over your servers private IP?', $input)
        ->run();

    expect($this->config->get('ssh_private_ip_access'))
        ->toBe($output);

})->with([['yes', true], ['no', false]]);
