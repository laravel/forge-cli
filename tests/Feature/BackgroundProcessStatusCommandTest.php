<?php

use Laravel\Forge\Resources\BackgroundProcess;
use Laravel\Forge\Resources\Server;

it('can retrieve background process status', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'restarting']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 2)->once()->andReturn(
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'restarting']),
    );

    $this->artisan('background-process:status')
        ->expectsSearch(
            'Which background process would you like to check',
            answer: 2,
            search: 'queue',
            answers: [2 => 'php8.1 artisan queue:work'],
        )
        ->expectsPromptsInfo('The background process is restarting.');
});

it('can retrieve background process status with the daemon alias', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    );

    $this->artisan('daemon:status', ['backgroundProcess' => 1])
        ->expectsPromptsInfo('The background process is installed.');
});
