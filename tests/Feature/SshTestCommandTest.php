<?php

it('can test ssh connections', function () {
    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->artisan('ssh:test')->assertExitCode(0);
});

it('can not test ssh connections when ssh key is missing', function () {
    $this->remote->shouldReceive('ensureSshIsConfigured')->andThrow(
        new Exception('Unable to connect to remove server. Have you configured an SSH Key?')
    );

    $this->artisan('ssh:test');
})->throws('Unable to connect to remove server. Have you configured an SSH Key?');
