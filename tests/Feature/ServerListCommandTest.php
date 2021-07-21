<?php

it('displays the list of servers', function () {
    $this->client->shouldReceive('servers')->andReturn([
        (object) ['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000'],
        (object) ['id' => 2, 'name' => 'staging', 'ipAddress' => '789.456.123.111'],
    ]);

    $this->artisan('server:list')
        ->expectsTable(['   ID', '   Name', '   IP Address'], [
            ['id' => '   1', 'name' => '   production', 'ipAddress' => '   123.456.789.000'],
            ['id' => '   2', 'name' => '   staging', 'ipAddress' => '   789.456.123.111'],
        ], 'compact');
});
