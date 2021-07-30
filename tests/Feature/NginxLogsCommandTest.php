<?php

it('can retrieve error logs from nginx', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('logs')
        ->with(Mockery::any(), 'nginx_error')
        ->andReturn((object) [
            'content' => "   [00:01] FOO\n[00:02] BAR\n   ",
        ]);

    $this->artisan('nginx:logs')
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can retrieve access logs from nginx', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('logs')
        ->with(Mockery::any(), 'nginx_access')
        ->andReturn((object) [
            'content' => "   [00:01] FOO\n[00:02] BAR\n   ",
        ]);

    $this->artisan('nginx:logs', ['type' => 'access'])
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can not retrieve logs from unknown types', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->artisan('nginx:logs', ['type' => 'something']);
})->throws('Log type must be either "error" or "access".');
