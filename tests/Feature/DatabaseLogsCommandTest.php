<?php

use Laravel\Forge\Resources\Server;

it('can retrieve logs from databases', function (string $databaseType, string $logKey) {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => $databaseType]),
    );

    $this->client->shouldReceive('serverLog')
        ->with('personal', 1, $logKey)
        ->andReturn("   [00:01] FOO\n[00:02] BAR\n   ");

    $this->artisan('database:logs')
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
})->with([
    ['mysql', 'database-mysql'],
    ['mysql8', 'database-mysql'],
    ['postgres', 'database-postgresql'],
    ['postgres13', 'database-postgresql'],
    ['mariadb', 'database-mariadb'],
]);

it('can not retrieve logs when there is no databases', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => null]),
    );

    $this->artisan('database:logs');
})->throws('No databases installed on this server.');

it('can not retrieve logs from unknown databases', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'nitro']),
    );

    $this->artisan('database:logs');
})->throws('Retrieving logs from [nitro] databases is not supported.');
