<?php

use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can deploy sites with a menu', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 1)->once()->andReturn(
        new Site(['id' => 1, 'name' => 'pestphp.com', 'deploymentStatus' => null]),
    );

    $this->client->shouldReceive('createDeployment')->with('personal', 1, 1)->once()->andReturn(
        new Deployment(['id' => 3, 'status' => 'queued']),
    );

    $this->client->shouldReceive('deployment')->with('personal', 1, 1, 3)->once()->andReturn(
        new Deployment([
            'id' => 3,
            'status' => 'finished',
            'startedAt' => '2021-07-20 12:50:01',
            'endedAt' => '2021-07-20 12:50:09',
        ]),
    );

    $this->client->shouldReceive('deploymentLog')->with('personal', 1, 1, 3)->once()->andReturn(
        "Installing composer dependencies...\nRestarting FPM...",
    );

    $this->artisan('deploy')
        ->expectsSearch(
            'Which site would you like to deploy',
            answer: 1,
            search: 'pestphp',
            answers: [1 => 'pestphp.com'],
        )
        ->expectsOutput('  ▕ Installing composer dependencies...')
        ->expectsOutput('  ▕ Restarting FPM...')
        ->expectsPromptsInfo('Site deployed successfully. (8s)');
});

it('can deploy sites with an option', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'deploymentStatus' => null]),
    );

    $this->client->shouldReceive('createDeployment')->with('personal', 1, 2)->once()->andReturn(
        new Deployment(['id' => 3, 'status' => 'queued']),
    );

    $this->client->shouldReceive('deployment')->with('personal', 1, 2, 3)->once()->andReturn(
        new Deployment([
            'id' => 3,
            'status' => 'finished',
            'startedAt' => '2021-07-20 12:50:01',
            'endedAt' => '2021-07-20 12:50:09',
        ]),
    );

    $this->client->shouldReceive('deploymentLog')->with('personal', 1, 2, 3)->once()->andReturn(
        "Installing composer dependencies...\nRestarting FPM...",
    );

    $this->artisan('deploy', ['site' => 2])
        ->expectsOutput('  ▕ Installing composer dependencies...')
        ->expectsOutput('  ▕ Restarting FPM...')
        ->expectsPromptsInfo('Site deployed successfully. (8s)');
});

it('can not deploy sites that are already deploying', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'deploymentStatus' => 'queued']),
    );

    $this->artisan('deploy', ['site' => 'something.com']);
})->throws('This site is already deploying.');

it('handles deployment failures', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'deploymentStatus' => null]),
    );

    $this->client->shouldReceive('createDeployment')->with('personal', 1, 2)->once()->andReturn(
        new Deployment(['id' => 3, 'status' => 'queued']),
    );

    $this->client->shouldReceive('deployment')->with('personal', 1, 2, 3)->once()->andReturn(
        new Deployment([
            'id' => 3,
            'status' => 'failed',
            'startedAt' => '2021-07-20 12:50:01',
            'endedAt' => '2021-07-20 12:50:09',
        ]),
    );

    $this->client->shouldReceive('deploymentLog')->with('personal', 1, 2, 3)->once()->andReturn(
        "Installing composer dependencies...\nRestarting FPM failed...",
    );

    $this->artisan('deploy', ['site' => 2]);
})->throws('The deployment failed.');
