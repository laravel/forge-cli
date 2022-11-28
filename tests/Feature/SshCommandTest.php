<?php

it('can create ssh connections', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->remote->shouldReceive('passthru')->andReturn(0);

    $this->artisan('ssh')->assertExitCode(0);
});

it('can connect to a specific ssh user', function() {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->remote->shouldReceive('passthru')
        ->with(null, 'testuser')
        ->andReturn(0);

    $this->artisan('ssh', [
        '--user' => 'testuser'
    ])->assertExitCode(0);
});

it('defaults to the forge user', function() {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->remote->shouldReceive('passthru')
        ->with(null, 'forge')
        ->andReturn(0);

    $this->artisan('ssh')->assertExitCode(0);
});

it('can not create ssh connections when ssh key is missing', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'name' => 'staging',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->remote->shouldReceive('passthru')->andReturn(255);

    $this->artisan('ssh');
})->throws('Unable to connect to remote server. Have you configured an SSH key?');
