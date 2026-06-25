<?php

use Laravel\Forge\Resources\BackgroundProcess;
use Laravel\Forge\Resources\Server;

it('can display the list of background processes', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4  artisan websockets:serve', 'status' => 'installed']),
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1  artisan queue:work', 'status' => 'installed']),
    ]));

    $this->artisan('background-process:list')
        ->expectsPromptsTable(['ID', 'Command', 'Status'], [
            [1, 'php7.4  artisan websockets:serve', 'Installed'],
            [2, 'php8.1  artisan queue:work', 'Installed'],
        ]);
});

it('can display the list of background processes with the daemon alias', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->artisan('daemon:list')
        ->expectsPromptsTable(['ID', 'Command', 'Status'], [
            [1, 'php8.1 artisan queue:work', 'Installed'],
        ]);
});

it('warns when there are no background processes', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([]));

    $this->artisan('background-process:list')
        ->expectsPromptsWarning('No background processes found.');
});
