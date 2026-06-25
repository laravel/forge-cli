<?php

use Laravel\Forge\Resources\Server;

it('can display the database status running', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222']),
    );

    $this->remote->shouldReceive('exec')
        ->with('systemctl is-active --quiet mysql')
        ->andReturn([0]);

    $this->artisan('database:status')
        ->expectsPromptsInfo('The database is up and running');
});

it('can display the database status as inactive', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222']),
    );

    $this->remote->shouldReceive('exec')
        ->with('systemctl is-active --quiet mysql')
        ->andReturn([3]);

    $this->artisan('database:status');
})->throws('Service is not running.');

it('can not display the status when there is no database', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => null]),
    );

    $this->artisan('database:status');
})->throws('No databases installed on this server.');
