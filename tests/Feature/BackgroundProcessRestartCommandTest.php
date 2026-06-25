<?php

use Laravel\Forge\Resources\BackgroundProcess;
use Laravel\Forge\Resources\Server;

it('can restart background processes', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 2)->once()->andReturn(
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    );

    $this->client->shouldReceive('performBackgroundProcessAction')->with('personal', 1, 2, ['action' => 'restart'])->once();

    $this->artisan('background-process:restart')
        ->expectsSearch(
            'Which background process would you like to restart',
            answer: 2,
            search: 'queue',
            answers: [2 => 'php8.1 artisan queue:work'],
        )
        ->expectsPromptsInfo('Background process restart initiated successfully.');
});

it('can restart background processes with the daemon alias', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    );

    $this->client->shouldReceive('performBackgroundProcessAction')->with('personal', 1, 1, ['action' => 'restart'])->once();

    $this->artisan('daemon:restart', ['backgroundProcess' => 1])
        ->expectsPromptsInfo('Background process restart initiated successfully.');
});

it('can not restart background processes that are not running', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'restarting']),
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'restarting']),
    );

    $this->artisan('background-process:restart')
        ->expectsSearch(
            'Which background process would you like to restart',
            answer: 1,
            search: 'websockets',
            answers: [1 => 'php7.4 artisan websockets:serve'],
        );
})->throws('This background process is not installed or running.');
