<?php

use Laravel\Forge\Resources\Server;

it('can test ssh connections', function () {
    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->artisan('ssh:test')
        ->assertExitCode(0)
        ->expectsPromptsInfo('SSH key based secure authentication is configured');
});

it('can switch servers before testing ssh connections', function () {
    $this->config->set('server', 1);

    $this->client->shouldReceive('servers')->with('personal')->once()->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'staging', 'revoked' => false]),
    ]));

    $this->client->shouldReceive('server')->with('personal', 2)->once()->andReturn(
        new Server(['id' => 2, 'name' => 'staging']),
    );

    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->artisan('ssh:test', ['server' => 'staging'])
        ->assertExitCode(0)
        ->expectsPromptsInfo('Current server context changed successfully to staging')
        ->expectsPromptsInfo('SSH key based secure authentication is configured');

    expect($this->config->get('server'))->toBe(2);
});

it('can not test ssh connections when ssh key is missing', function () {
    $this->remote->shouldReceive('ensureSshIsConfigured')->andThrow(
        new Exception('Unable to connect to remote server. Have you configured an SSH Key?')
    );

    $this->artisan('ssh:test');
})->throws('Unable to connect to remote server. Have you configured an SSH Key?');
