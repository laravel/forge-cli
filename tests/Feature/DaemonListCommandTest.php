<?php

it('can display the list of daemons', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4  artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.0  artisan queue:work', 'status' => 'installed'],
    ]);

    $this->artisan('daemon:list')
        ->expectsTable(['   ID', '   Command', '   Status'], [
            ['id' => '   1', 'name' => '   php7.4  artisan websockets:serve', 'phpVersion' => '   Installed'],
            ['id' => '   2', 'name' => '   php8.0  artisan queue:work', 'phpVersion' => '   Installed'],
        ], 'compact');
});
