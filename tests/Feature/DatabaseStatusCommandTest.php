<?php

it('allows to know the database status with an menu', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('databases')->andReturn([
        (object) ['id' => 1, 'name' => 'database-a', 'status' => 'installed'],
        (object) ['id' => 2, 'name' => 'database-b', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('database')->andReturn(
        (object) ['id' => 2, 'name' => 'database-b', 'status' => 'installed'],
    );

    $exitCode = $this->artisan('database:status')
        ->expectsChoice('Which database would you like to know the current status?', 'database-b', [
            'database-a', 'database-b',
        ])->expectsOutput('The database [database-b] is [installed].')->run();
});

it('allows to know the database status with an option', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('database')->andReturn(
        (object) ['id' => 2, 'name' => 'database-b', 'status' => 'installed'],
    );

    $exitCode = $this->artisan('database:status', ['--id' => 2])
        ->expectsOutput('The database [database-b] is [installed].')
        ->run();
});
