<?php

it('can open shell connections to mysql databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222'],
    );

    $this->client->shouldReceive('databases')->andReturn([
        (object) ['id' => 1, 'name' => 'forge-default-database'],
    ]);

    $this->remote->shouldReceive('passthru')
        ->with('mysql -u forge -pmy-secret-password forge-default-database')
        ->andReturn(0);

    $this->artisan('database:shell')
        ->expectsOutput('==> Establishing Shell Connection To [production@forge-default-database] Database')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Enter The Database User <comment>[forge]</comment> Password</>', 'my-secret-password');
});

it('can open shell connections to postgres databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'postgres13', 'ipAddress' => '123.456.789.222'],
    );

    $this->client->shouldReceive('databases')->andReturn([
        (object) ['id' => 1, 'name' => 'forge-default-database'],
    ]);

    $this->remote->shouldReceive('passthru')
        ->with('PGPASSWORD=my-secret-password psql -U forge forge-default-database')
        ->andReturn(0);

    $this->artisan('database:shell')
        ->expectsOutput('==> Establishing Shell Connection To [production@forge-default-database] Database')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Enter The Database User <comment>[forge]</comment> Password</>', 'my-secret-password');
});

test('exit code gets returned', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222'],
    );

    $this->client->shouldReceive('databases')->andReturn([
        (object) ['id' => 1, 'name' => 'forge-default-database'],
    ]);

    $this->remote->shouldReceive('passthru')
        ->with('mysql -u forge -pmy-wrong-secret-password forge-default-database')
        ->andReturn(1);

    $this->artisan('database:shell')
        ->expectsOutput('==> Establishing Shell Connection To [production@forge-default-database] Database')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Enter The Database User <comment>[forge]</comment> Password</>', 'my-wrong-secret-password')
        ->assertExitCode(1);
});

it('can open shell connections with custom database name and user', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'postgres13', 'ipAddress' => '123.456.789.222'],
    );

    $this->remote->shouldReceive('passthru')
        ->with('PGPASSWORD=my-secret-password psql -U my-user my-database')
        ->andReturn(0);

    $this->artisan('database:shell', ['database' => 'my-database', '--user' => 'my-user'])
        ->expectsOutput('==> Establishing Shell Connection To [production@my-database] Database')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Enter The Database User <comment>[my-user]</comment> Password</>', 'my-secret-password');
});

it('can not open shell connections to database if there is no databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222'],
    );

    $this->client->shouldReceive('databases')->andReturn([]);

    $this->artisan('database:shell');
})->throws('No databases found.');

it('can not open shell connections if the database is empty', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222'],
    );

    $this->client->shouldReceive('databases')->andReturn([
        (object) ['id' => 1, 'name' => 'forge-default-database'],
    ]);

    $this->artisan('database:shell')
        ->expectsOutput('==> Establishing Shell Connection To [production@forge-default-database] Database')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Enter The Database User <comment>[forge]</comment> Password</>', null);
})->throws('Password can not be empty.');

it('can not open shell connections when there is not database service', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => null, 'ipAddress' => '123.456.789.222'],
    );

    $this->artisan('database:shell');
})->throws('No databases installed in this server.');
