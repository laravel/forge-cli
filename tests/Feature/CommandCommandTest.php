<?php

use Laravel\Forge\Resources\Command;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can run commands on sites with a menu', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('createCommand')->with('personal', 1, 1, ['command' => 'php artisan clear'])->once();

    $this->client->shouldReceive('commands')->with('personal', 1, 1, ['sort' => '-created_at', 'page[size]' => 1])->once()->andReturn(fakePaginator([
        new Command(['id' => 4, 'command' => 'php artisan clear', 'status' => 'running']),
    ]));

    $this->client->shouldReceive('command')->with('personal', 1, 1, 4)->once()->andReturn(
        new Command(['id' => 4, 'command' => 'php artisan clear', 'status' => 'finished']),
    );

    $this->client->shouldReceive('commandOutput')->with('personal', 1, 1, 4)->once()->andReturn(
        'Compiled services and packages files removed!',
    );

    $this->artisan('command')
        ->expectsSearch(
            'Which site would you like to run the command on',
            answer: 1,
            search: 'pestphp',
            answers: [1 => 'pestphp.com'],
        )
        ->expectsQuestion('What command would you like to execute', 'php artisan clear')
        ->expectsOutput('  ▕ Compiled services and packages files removed!')
        ->expectsPromptsInfo('Command run successfully.');
});

it('can run commands with an option', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('createCommand')->with('personal', 1, 2, ['command' => 'php artisan list'])->once();

    $this->client->shouldReceive('commands')->with('personal', 1, 2, ['sort' => '-created_at', 'page[size]' => 1])->once()->andReturn(fakePaginator([
        new Command(['id' => 3, 'command' => 'php artisan list', 'status' => 'running']),
    ]));

    $this->client->shouldReceive('command')->with('personal', 1, 2, 3)->once()->andReturn(
        new Command(['id' => 3, 'command' => 'php artisan list', 'status' => 'finished']),
    );

    $this->client->shouldReceive('commandOutput')->with('personal', 1, 2, 3)->once()->andReturn(
        'Compiled services and packages files removed!',
    );

    $this->artisan('command', ['site' => 2, '--command' => 'php artisan list'])
        ->expectsOutput('  ▕ Compiled services and packages files removed!')
        ->expectsPromptsInfo('Command run successfully.');
});

it('handles command failures', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('createCommand')->with('personal', 1, 2, ['command' => 'php artisan migrate'])->once();

    $this->client->shouldReceive('commands')->with('personal', 1, 2, ['sort' => '-created_at', 'page[size]' => 1])->once()->andReturn(fakePaginator([
        new Command(['id' => 3, 'command' => 'php artisan migrate', 'status' => 'running']),
    ]));

    $this->client->shouldReceive('command')->with('personal', 1, 2, 3)->once()->andReturn(
        new Command(['id' => 3, 'command' => 'php artisan migrate', 'status' => 'failed']),
    );

    $this->client->shouldReceive('commandOutput')->with('personal', 1, 2, 3)->once()->andReturn(
        ' Illuminate\Database\QueryException',
    );

    $this->artisan('command', ['site' => 'something.com', '--command' => 'php artisan migrate']);
})->throws('The command failed.');

it('runs the command that was just created instead of the oldest one', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
    ]));

    $this->client->shouldReceive('createCommand')->with('personal', 1, 1, ['command' => 'php artisan about'])->once();

    // Without a sort, the API lists the oldest commands first...
    $this->client->shouldReceive('commands')->with('personal', 1, 1)->andReturn(fakePaginator([
        new Command(['id' => 1, 'command' => 'php artisan list', 'status' => 'finished']),
        new Command(['id' => 2, 'command' => 'php artisan about', 'status' => 'running']),
    ]));

    // ...and the latest first when sorted by descending creation date.
    $this->client->shouldReceive('commands')->with('personal', 1, 1, ['sort' => '-created_at', 'page[size]' => 1])->andReturn(fakePaginator([
        new Command(['id' => 2, 'command' => 'php artisan about', 'status' => 'running']),
    ]));

    $this->client->shouldReceive('command')->with('personal', 1, 1, 2)->andReturn(
        new Command(['id' => 2, 'command' => 'php artisan about', 'status' => 'finished']),
    );

    $this->client->shouldReceive('commandOutput')->with('personal', 1, 1, 1)->andReturn('Output of the oldest command');
    $this->client->shouldReceive('commandOutput')->with('personal', 1, 1, 2)->andReturn('Output of the new command');

    $this->artisan('command', ['site' => 1, '--command' => 'php artisan about'])
        ->expectsOutput('  ▕ Output of the new command')
        ->doesntExpectOutput('  ▕ Output of the oldest command')
        ->expectsPromptsInfo('Command run successfully.');
});
