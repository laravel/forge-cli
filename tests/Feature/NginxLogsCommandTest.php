<?php

use Laravel\Forge\Resources\Server;

it('can retrieve error logs from nginx', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverLog')
        ->with('personal', 1, 'nginx-error')
        ->andReturn("   [00:01] FOO\n[00:02] BAR\n   ");

    $this->artisan('nginx:logs')
        ->expectsPromptsInfo('Retrieving the latest error logs')
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can retrieve access logs from nginx', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverLog')
        ->with('personal', 1, 'nginx-access')
        ->andReturn("   [00:01] FOO\n[00:02] BAR\n   ");

    $this->artisan('nginx:logs', ['type' => 'access'])
        ->expectsPromptsInfo('Retrieving the latest access logs')
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can not retrieve logs from unknown types', function () {
    $this->artisan('nginx:logs', ['type' => 'something']);
})->throws('Log type must be "error" or "access".');
