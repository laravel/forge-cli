<?php

it('displays the list of databases', function () {
    $this->client->shouldReceive('databases')->andReturn([
        (object) ['id' => 1, 'name' => 'production', 'status' => 'installed'],
        (object) ['id' => 2, 'name' => 'staging', 'status' => 'installed'],
    ]);

    $this->artisan('database:list')
        ->expectsTable(['ID', 'Name', 'IP Address'], [
            ['id' => 1, 'name' => 'production', 'status' => 'installed'],
            ['id' => 2, 'name' => 'staging', 'status' => 'installed'],
        ]);
});
