<?php

use Laravel\Forge\Resources\Database;
use Laravel\Forge\Resources\Server;

it('can open shell connections to mysql databases', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222']),
    );

    $this->client->shouldReceive('databases')->with('personal', 1)->andReturn(fakePaginator([
        new Database(['id' => 1, 'name' => 'forge-default-database']),
    ]));

    $this->remote->shouldReceive('passthru')
        ->with('mysql -u forge -pmy-secret-password forge-default-database')
        ->andReturn(0);

    $this->artisan('database:shell')
        ->expectsPromptsInfo('Establishing shell connection to production@forge-default-database database')
        ->expectsQuestion('Enter the password for database user forge', 'my-secret-password');
});

it('can open shell connections to postgres databases', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'postgres13', 'ipAddress' => '123.456.789.222']),
    );

    $this->client->shouldReceive('databases')->with('personal', 1)->andReturn(fakePaginator([
        new Database(['id' => 1, 'name' => 'forge-default-database']),
    ]));

    $this->remote->shouldReceive('passthru')
        ->with('PGPASSWORD=my-secret-password psql -U forge forge-default-database')
        ->andReturn(0);

    $this->artisan('database:shell')
        ->expectsPromptsInfo('Establishing shell connection to production@forge-default-database database')
        ->expectsQuestion('Enter the password for database user forge', 'my-secret-password');
});

test('exit code gets returned', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222']),
    );

    $this->client->shouldReceive('databases')->with('personal', 1)->andReturn(fakePaginator([
        new Database(['id' => 1, 'name' => 'forge-default-database']),
    ]));

    $this->remote->shouldReceive('passthru')
        ->with('mysql -u forge -pmy-wrong-secret-password forge-default-database')
        ->andReturn(1);

    $this->artisan('database:shell')
        ->expectsPromptsInfo('Establishing shell connection to production@forge-default-database database')
        ->expectsQuestion('Enter the password for database user forge', 'my-wrong-secret-password')
        ->assertExitCode(1);
});

it('can open shell connections with custom database name and user', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'postgres13', 'ipAddress' => '123.456.789.222']),
    );

    $this->remote->shouldReceive('passthru')
        ->with('PGPASSWORD=my-secret-password psql -U my-user my-database')
        ->andReturn(0);

    $this->artisan('database:shell', ['database' => 'my-database', '--user' => 'my-user'])
        ->expectsPromptsInfo('Establishing shell connection to production@my-database database')
        ->expectsQuestion('Enter the password for database user my-user', 'my-secret-password');
});

it('can not open shell connections to database if there is no databases', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222']),
    );

    $this->client->shouldReceive('databases')->with('personal', 1)->andReturn(fakePaginator([]));

    $this->artisan('database:shell');
})->throws('No databases found.');

it('can not open shell connections if the database is empty', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222']),
    );

    $this->client->shouldReceive('databases')->with('personal', 1)->andReturn(fakePaginator([
        new Database(['id' => 1, 'name' => 'forge-default-database']),
    ]));

    $this->artisan('database:shell')
        ->expectsPromptsInfo('Establishing shell connection to production@forge-default-database database')
        ->expectsQuestion('Enter the password for database user forge', null);
})->throws('Password can not be empty.');

it('can not open shell connections when there is not database service', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'databaseType' => null, 'ipAddress' => '123.456.789.222']),
    );

    $this->artisan('database:shell');
})->throws('No databases installed on this server.');
