<?php

use Laravel\Forge\Resources\Server;

it('displays the list of servers', function () {
    $this->client->shouldReceive('servers')->with('personal')->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'staging', 'ipAddress' => '789.456.123.111', 'revoked' => false]),
    ]));

    $this->artisan('server:list')
        ->expectsPromptsTable(['ID', 'Name', 'IP Address'], [
            [1, 'production', '123.456.789.000'],
            [2, 'staging', '789.456.123.111'],
        ]);
});

it('does not display revoked servers', function () {
    $this->client->shouldReceive('servers')->with('personal')->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'revoked', 'ipAddress' => '789.456.123.111', 'revoked' => true]),
    ]));

    $this->artisan('server:list')
        ->expectsPromptsTable(['ID', 'Name', 'IP Address'], [
            [1, 'production', '123.456.789.000'],
        ]);
});
