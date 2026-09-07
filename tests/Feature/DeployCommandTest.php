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

it('defaults the polling interval option to 5 seconds', function () {
    $command = $this->app[Illuminate\Contracts\Console\Kernel::class]->all()['deploy'];

    expect($command->getDefinition()->getOption('interval')->getDefault())->toBe('5');
});

it('defaults the timeout option to 900 seconds', function () {
    $command = $this->app[Illuminate\Contracts\Console\Kernel::class]->all()['deploy'];

    expect($command->getDefinition()->getOption('timeout')->getDefault())->toBe('900');
});

it('times out if the deployment does not finish before the timeout', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'deploymentStatus' => null]),
    );

    $this->client->shouldReceive('createDeployment')->with('personal', 1, 2)->once()->andReturn(
        new Deployment(['id' => 3, 'status' => 'queued']),
    );

    $this->client->shouldReceive('deployment')->with('personal', 1, 2, 3)->once()->andReturn(
        new Deployment(['id' => 3, 'status' => 'deploying']),
    );

    $this->artisan('deploy', ['site' => 2, '--interval' => 1, '--timeout' => 1]);
})->throws('Timed out waiting for the deployment to finish.');

it('times out based on elapsed time rather than time spent sleeping', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'deploymentStatus' => null]),
    );

    $this->client->shouldReceive('createDeployment')->with('personal', 1, 2)->once()->andReturn(
        new Deployment(['id' => 3, 'status' => 'queued']),
    );

    // A hanging API call still counts towards the timeout, even though
    // the command never gets the chance to sleep between polls.
    $this->client->shouldReceive('deployment')->with('personal', 1, 2, 3)->once()->andReturnUsing(function () {
        usleep(1_100_000);

        return new Deployment(['id' => 3, 'status' => 'deploying']);
    });

    $this->artisan('deploy', ['site' => 2, '--interval' => 0, '--timeout' => 1]);
})->throws('Timed out waiting for the deployment to finish.');

it('does not time out when the timeout option is 0', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com', 'deploymentStatus' => null]),
    );

    $this->client->shouldReceive('createDeployment')->with('personal', 1, 2)->once()->andReturn(
        new Deployment(['id' => 3, 'status' => 'queued']),
    );

    $this->client->shouldReceive('deployment')->with('personal', 1, 2, 3)->twice()->andReturn(
        new Deployment(['id' => 3, 'status' => 'deploying']),
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

    $this->artisan('deploy', ['site' => 2, '--interval' => 1, '--timeout' => 0])
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
