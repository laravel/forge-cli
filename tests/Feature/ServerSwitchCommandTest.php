<?php

use Laravel\Forge\Resources\Server;

it('allows to switch the server context with a menu', function () {
    $this->client->shouldReceive('servers')->with('personal')->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'staging', 'revoked' => false]),
    ]));

    $this->client->shouldReceive('server')->with('personal', 2)->andReturn(
        new Server(['id' => 2, 'name' => 'staging']),
    );

    $this->artisan('server:switch')
        ->expectsSearch(
            'Which server would you like to switch to',
            answer: 2,
            search: 'staging',
            answers: [2 => 'staging'],
        )
        ->expectsPromptsInfo('Current server context changed successfully to staging');

    expect($this->config->get('server'))->toBe(2);
});

it('allows to switch the server context with an argument', function () {
    $this->client->shouldReceive('servers')->with('personal')->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'staging', 'revoked' => false]),
    ]));

    $this->client->shouldReceive('server')->with('personal', 2)->andReturn(
        new Server(['id' => 2, 'name' => 'staging']),
    );

    $this->artisan('server:switch', ['server' => 'staging'])
        ->expectsPromptsInfo('Current server context changed successfully to staging');

    expect($this->config->get('server'))->toBe(2);
});
