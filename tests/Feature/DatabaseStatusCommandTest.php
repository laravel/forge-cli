<?php

it('can display the database status active', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222'],
    );

    $this->shell->shouldReceive('exec')->andReturn([0]);

    $this->artisan('database:status')->expectsOutput('The database is [active].');
});

it('can display the database status as inactive', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222'],
    );

    $this->shell->shouldReceive('exec')->andReturn([3]);

    $this->artisan('database:status')->expectsOutput('The database is [inactive].');
});

it('can not display the status when there is no database', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => null],
    );

    $this->artisan('database:status');
})->throws('No database available.');
