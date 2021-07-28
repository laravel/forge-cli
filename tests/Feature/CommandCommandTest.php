<?php

it('can run commands on sites with an menu', function () {
    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('executeSiteCommand')->once()->andReturn((object) [
        'id' => 4,
    ]);

    $this->client->shouldReceive('events')->with(1)->once()->andReturn([
        (object) ['id' => 6, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 5, 'description' => 'Running Custom Command.'],
        (object) ['id' => 1, 'description' => 'Deploying Pushed Code (pestphp.com).'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 1, 4)->once()->andReturn([
        (object) ['id' => 4, 'status' => 'running'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 1, 4)->once()->andReturn([
        (object) ['id' => 4, 'status' => 'finished'],
    ]);

    $this->client->shouldReceive('site')->with(1, 1)->once()->andReturn(
        (object) ['id' => 1, 'username' => 'forge'],
    );

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-5.output'
    )->twice()->andReturn([0, [
        'Compiled services and packages files removed!',
    ]]);

    $this->artisan('command')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Run The Command On</>', 1)
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>What Command Would You Like To Execute</>', 'php artisan clear')
        ->expectsOutput('==> Queuing Command')
        ->expectsOutput('==> Waiting For Command To Run')
        ->expectsOutput('==> Running')
        ->expectsOutput('  ▕ Compiled services and packages files removed!')
        ->expectsOutput('==> Command Run Successfully.');
});

it('can run commands with an option', function () {
    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('executeSiteCommand')->once()->andReturn((object) [
        'id' => 3,
    ]);

    $this->client->shouldReceive('events')->with(1)->once()->andReturn([
        (object) ['id' => 6, 'description' => 'Running Custom Command.'],
        (object) ['id' => 5, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 1, 'description' => 'Deploying Pushed Code (pestphp.com).'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 2, 3)->once()->andReturn([
        (object) ['id' => 3, 'status' => 'running'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 2, 3)->once()->andReturn([
        (object) ['id' => 3, 'status' => 'finished'],
    ]);

    $this->client->shouldReceive('site')->with(1, 2)->once()->andReturn(
        (object) ['id' => 1, 'username' => 'forge'],
    );

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-6.output'
    )->twice()->andReturn([0, [
        'Compiled services and packages files removed!',
    ]]);

    $this->artisan('command', ['site' => 2, '--command' => 'php artisan list'])
        ->expectsOutput('==> Queuing Command')
        ->expectsOutput('==> Waiting For Command To Run')
        ->expectsOutput('==> Running')
        ->expectsOutput('  ▕ Compiled services and packages files removed!')
        ->expectsOutput('==> Command Run Successfully.');
});

it('can run commands when sites use website isolation', function () {
    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('executeSiteCommand')->once()->andReturn((object) [
        'id' => 3,
    ]);

    $this->client->shouldReceive('events')->with(1)->once()->andReturn([
        (object) ['id' => 6, 'description' => 'Running Custom Command.'],
        (object) ['id' => 5, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 1, 'description' => 'Deploying Pushed Code (pestphp.com).'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 2, 3)->once()->andReturn([
        (object) ['id' => 3, 'status' => 'running'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 2, 3)->once()->andReturn([
        (object) ['id' => 3, 'status' => 'finished'],
    ]);

    $this->client->shouldReceive('site')->with(1, 2)->once()->andReturn(
        (object) ['id' => 1, 'username' => 'user-in-isolation'],
    );

    $this->remote->shouldReceive('exec')->with(
        'cat /home/user-in-isolation/.forge/provision-6.output'
    )->twice()->andReturn([0, [
        'Compiled services and packages files removed!',
    ]]);

    $this->artisan('command', ['site' => 2, '--command' => 'php artisan list'])
        ->expectsOutput('==> Queuing Command')
        ->expectsOutput('==> Waiting For Command To Run')
        ->expectsOutput('==> Running')
        ->expectsOutput('  ▕ Compiled services and packages files removed!')
        ->expectsOutput('==> Command Run Successfully.');
});

it('handles command failures', function () {
    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('executeSiteCommand')->once()->andReturn((object) [
        'id' => 3,
    ]);

    $this->client->shouldReceive('events')->with(1)->once()->andReturn([
        (object) ['id' => 6, 'description' => 'Running Custom Command.'],
        (object) ['id' => 5, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 1, 'description' => 'Deploying Pushed Code (pestphp.com).'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 2, 3)->once()->andReturn([
        (object) ['id' => 3, 'status' => 'running'],
    ]);

    $this->client->shouldReceive('getSiteCommand')->with(1, 2, 3)->once()->andReturn([
        (object) ['id' => 3, 'status' => 'failed'],
    ]);

    $this->client->shouldReceive('site')->with(1, 2)->once()->andReturn(
        (object) ['id' => 1, 'username' => 'user-in-isolation'],
    );

    $this->remote->shouldReceive('exec')->with(
        'cat /home/user-in-isolation/.forge/provision-6.output'
    )->twice()->andReturn([0, [
        ' Illuminate\Database\QueryException',
    ]]);

    $this->artisan('command', ['site' => 'something.com', '--command' => 'php artisan migrate']);
})->throws('The command failed.');
