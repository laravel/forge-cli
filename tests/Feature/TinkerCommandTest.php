<?php

use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can tinker with sites', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'phpVersion' => 'php71', 'user' => 'user-in-isolation']),
    );

    $this->remote
        ->shouldReceive('passthru')
        ->with('cd /home/user-in-isolation/something.com && php7.1 artisan tinker')
        ->andReturn(0);

    $this->artisan('tinker', ['site' => 2])
        ->assertExitCode(0)
        ->expectsPromptsInfo('Establishing tinker connection');
});

it('can tinker with zero downtime sites', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'phpVersion' => 'php71', 'user' => 'forge', 'zeroDowntimeDeployments' => true]),
    );

    $this->remote
        ->shouldReceive('passthru')
        ->with('cd /home/forge/something.com/current && php7.1 artisan tinker')
        ->andReturn(0);

    $this->artisan('tinker', ['site' => 2])
        ->assertExitCode(0)
        ->expectsPromptsInfo('Establishing tinker connection');
});
