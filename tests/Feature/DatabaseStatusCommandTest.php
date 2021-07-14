<?php

it('can display the database status', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql', 'ipAddress' => '123.456.789.222'],
    );

    $this->shell->shouldReceive('exec')->andReturn([0]);

    $this->artisan('database:status')->expectsOutput('The database is [active].');
});

it('can not restart when there is no databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => null],
    );

    $this->artisan('database:status');
})->throws('No database available.');
