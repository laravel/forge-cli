<?php

use Laravel\Forge\Resources\Server;

it('can create ssh connections', function () {
    $this->config->set('server', 1);

    $this->client->shouldReceive('server')
        ->once()
        ->with('personal', 1)
        ->andReturn(new Server([
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]));

    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->remote->shouldReceive('passthru')->andReturn(0);

    $this->artisan('ssh')
        ->assertExitCode(0)
        ->expectsPromptsInfo('Connected to production');
});

it('can connect to a specific ssh user', function () {
    $this->config->set('server', 1);

    $this->client->shouldReceive('server')
        ->once()
        ->with('personal', 1)
        ->andReturn(new Server([
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]));

    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->remote->shouldReceive('passthru')
        ->with(null, 'testuser')
        ->andReturn(0);

    $this->artisan('ssh', [
        '--user' => 'testuser',
    ])
        ->assertExitCode(0)
        ->expectsPromptsInfo('Connected to production');
});

it('defaults to the forge user', function () {
    $this->config->set('server', 1);

    $this->client->shouldReceive('server')
        ->once()
        ->with('personal', 1)
        ->andReturn(new Server([
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]));

    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->remote->shouldReceive('passthru')
        ->with(null, 'forge')
        ->andReturn(0);

    $this->artisan('ssh')
        ->assertExitCode(0)
        ->expectsPromptsInfo('Connected to production');
});

it('can switch servers before creating ssh connections', function () {
    $this->config->set('server', 1);

    $this->client->shouldReceive('servers')->with('personal')->once()->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'staging', 'revoked' => false]),
    ]));

    $this->client->shouldReceive('server')
        ->twice()
        ->with('personal', 2)
        ->andReturn(new Server([
            'id' => 2,
            'name' => 'staging',
            'ipAddress' => '123.456.789.000',
        ]));

    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->remote->shouldReceive('passthru')
        ->with(null, 'forge')
        ->andReturn(0);

    $this->artisan('ssh', ['server' => 'staging'])
        ->assertExitCode(0)
        ->expectsPromptsInfo('Current server context changed successfully to staging')
        ->expectsPromptsInfo('Connected to staging');

    expect($this->config->get('server'))->toBe(2);
});

it('can not create ssh connections when ssh key is missing', function () {
    $this->config->set('server', 1);

    $this->client->shouldReceive('server')
        ->once()
        ->with('personal', 1)
        ->andReturn(new Server([
            'name' => 'staging',
            'ipAddress' => '123.456.789.000',
        ]));

    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->remote->shouldReceive('passthru')->andReturn(255);

    $this->artisan('ssh');
})->throws('Unable to connect to remote server. Have you configured an SSH key?');
