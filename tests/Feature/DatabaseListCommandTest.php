<?php

it('displays the list of databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('databases')->andReturn([
        (object) ['id' => 1, 'name' => 'database-a', 'status' => 'installed'],
        (object) ['id' => 2, 'name' => 'database-b', 'status' => 'installed'],
    ]);

    $this->artisan('database:list')
        ->expectsTable(['ID', 'Name', 'Status'], [
            ['id' => 1, 'name' => 'database-a', 'status' => 'installed'],
            ['id' => 2, 'name' => 'database-b', 'status' => 'installed'],
        ]);
});
