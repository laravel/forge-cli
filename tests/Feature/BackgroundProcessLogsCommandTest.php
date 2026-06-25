<?php

use Laravel\Forge\Resources\BackgroundProcess;
use Laravel\Forge\Resources\Server;

it('can retrieve logs from background processes', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 2)->once()->andReturn(
        new BackgroundProcess(['id' => 2, 'user' => 'forge']),
    );

    $this->client->shouldReceive('backgroundProcessLog')->with('personal', 1, 2)->once()->andReturn(
        "   [00:01] FOO\n[00:02] BAR\n   ",
    );

    $this->artisan('background-process:logs')
        ->expectsSearch(
            'Which background process would you like to retrieve the logs from',
            answer: 2,
            search: 'queue',
            answers: [2 => 'php8.1 artisan queue:work'],
        )
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can retrieve logs with the daemon alias', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'user' => 'forge']),
    );

    $this->client->shouldReceive('backgroundProcessLog')->with('personal', 1, 1)->once()->andReturn(
        "   [00:01] FOO\n[00:02] BAR\n   ",
    );

    $this->artisan('daemon:logs', ['backgroundProcess' => 1])
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can tail logs from background processes', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'user' => 'forge']),
    );

    $this->remote->shouldReceive('tail')
        ->once()
        ->with('/home/forge/.forge/daemon-1.log', Mockery::type(Closure::class), ['-f'])
        ->andReturn([255, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('background-process:logs', ['--follow' => true])
        ->expectsSearch(
            'Which background process would you like to retrieve the logs from',
            answer: 1,
            search: 'websockets',
            answers: [1 => 'php7.4 artisan websockets:serve'],
        )
        ->expectsPromptsInfo('Retrieving the latest background process logs');
});

it('exits with 0 exit code on control + c', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
        new BackgroundProcess(['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'user' => 'forge']),
    );

    $this->remote->shouldReceive('tail')
        ->once()
        ->with('/home/forge/.forge/daemon-1.log', Mockery::type(Closure::class), ['-f'])
        ->andReturn([255, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('background-process:logs', ['--follow' => true])
        ->assertExitCode(0)
        ->expectsSearch(
            'Which background process would you like to retrieve the logs from',
            answer: 1,
            search: 'websockets',
            answers: [1 => 'php7.4 artisan websockets:serve'],
        )
        ->expectsPromptsInfo('Retrieving the latest background process logs');
});

it('displays empty log errors', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'user' => 'forge']),
    );

    $this->client->shouldReceive('backgroundProcessLog')->with('personal', 1, 1)->once()->andReturn('');

    $this->artisan('background-process:logs')
        ->expectsSearch(
            'Which background process would you like to retrieve the logs from',
            answer: 1,
            search: 'websockets',
            answers: [1 => 'php7.4 artisan websockets:serve'],
        );
})->throws('The requested logs could not be found or they are empty.');

it('displays tail errors', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'user' => 'forge']),
    );

    $this->remote->shouldReceive('tail')
        ->once()
        ->with('/home/forge/.forge/daemon-1.log', Mockery::type(Closure::class), ['-f'])
        ->andReturn(1);

    $this->artisan('background-process:logs', ['--follow' => true])
        ->expectsSearch(
            'Which background process would you like to retrieve the logs from',
            answer: 1,
            search: 'websockets',
            answers: [1 => 'php7.4 artisan websockets:serve'],
        )
        ->expectsPromptsInfo('Retrieving the latest background process logs');
})->throws('The requested logs could not be found or they are empty.');

it('can not tail logs from background processes run by root', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('backgroundProcesses')->with('personal', 1)->andReturn(fakePaginator([
        new BackgroundProcess(['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed']),
    ]));

    $this->client->shouldReceive('backgroundProcess')->with('personal', 1, 1)->once()->andReturn(
        new BackgroundProcess(['id' => 1, 'user' => 'root']),
    );

    $this->artisan('background-process:logs', ['--follow' => true])
        ->expectsSearch(
            'Which background process would you like to retrieve the logs from',
            answer: 1,
            search: 'websockets',
            answers: [1 => 'php7.4 artisan websockets:serve'],
        );
})->throws('Following logs from background processes run by [root] is not supported.');
