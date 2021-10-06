<?php

it('can test ssh connections', function () {
    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->artisan('ssh:test')
        ->assertExitCode(0)
        ->expectsOutput('==> Establishing Secure Connection')
        ->expectsOutput('==> SSH Key Based Secure Authentication Is Configured');
});

it('can not test ssh connections when ssh key is missing', function () {
    $this->remote->shouldReceive('ensureSshIsConfigured')->andThrow(
        new Exception('Unable to connect to remote server. Have you configured an SSH Key?')
    );

    $this->artisan('ssh:test');
})->throws('Unable to connect to remote server. Have you configured an SSH Key?');
