<?php

use Laravel\Forge\Resources\Server;

it('can restart mysql databases', function () {
    $server = Mockery::mock(Server::class)->makePartial();
    $server->id = 1;
    $server->name = 'production';
    $server->databaseType = 'mysql';
    $server->shouldReceive('rebootMysql')->once();

    $this->client->shouldReceive('server')->with('personal', 1)->andReturn($server);

    $this->artisan('database:restart')
        ->expectsConfirmation(
            'The database may become unavailable while the MySQL service restarts. Continue?',
            'yes',
        )->expectsPromptsInfo('Database restart initiated successfully.');
});

it('can restart postgres databases', function () {
    $server = Mockery::mock(Server::class)->makePartial();
    $server->id = 1;
    $server->name = 'production';
    $server->databaseType = 'postgres';
    $server->shouldReceive('rebootPostgres')->once();

    $this->client->shouldReceive('server')->with('personal', 1)->andReturn($server);

    $this->artisan('database:restart')
        ->expectsConfirmation(
            'The database may become unavailable while the PostgreSQL service restarts. Continue?',
            'yes'
        )->expectsPromptsInfo('Database restart initiated successfully.');
});

it('can not restart when there is no database', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => null]),
    );

    $this->artisan('database:restart');
})->throws('No databases installed on this server.');

it('can not restart unknown databases', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'nitro']),
    );

    $this->artisan('database:restart');
})->throws('Restarting [nitro] databases is not supported.');
