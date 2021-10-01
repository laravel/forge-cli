<?php

use Laravel\Forge\Resources\Server;

it('displays the list of servers', function () {
    $this->client->shouldReceive('servers')->andReturn([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000', 'tags' => [['name' => 'first'], ['name' => 'second']]]),
        new Server(['id' => 2, 'name' => 'staging', 'ipAddress' => '789.456.123.111', 'tags' => []]),
        new Server(['id' => 3, 'name' => 'acceptance', 'ipAddress' => '222.345.666.789', 'tags' => [['name' => 'first']]]),
    ]);

    $this->artisan('server:list')
        ->expectsTable(['   ID', '   Name', '   IP Address', '   Tags'], [
            ['id' => '   1', 'name' => '   production', 'ipAddress' => '   123.456.789.000', 'tags' => '   first, second'],
            ['id' => '   2', 'name' => '   staging', 'ipAddress' => '   789.456.123.111', 'tags' => '   '],
            ['id' => '   3', 'name' => '   acceptance', 'ipAddress' => '   222.345.666.789', 'tags' => '   first'],
        ], 'compact');
});
