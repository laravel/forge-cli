<?php

use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can retrieve deployment logs from sites with an menu', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('deployments')->with('personal', 1, 2)->once()->andReturn(fakePaginator([
        new Deployment(['id' => 3]),
    ]));

    $this->client->shouldReceive('deploymentLog')->with('personal', 1, 2, 3)->once()->andReturn(
        'Restarting FPM...',
    );

    $this->artisan('deploy:logs')
        ->expectsSearch(
            'Which site would you like to retrieve the deployment logs from',
            answer: 2,
            search: 'something',
            answers: [2 => 'something.com'],
        )
        ->expectsOutput('  ▕ Restarting FPM...');
});

it('can retrieve deployment logs from sites with an option', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('deployments')->with('personal', 1, 1)->once()->andReturn(fakePaginator([
        new Deployment(['id' => 4]),
    ]));

    $this->client->shouldReceive('deploymentLog')->with('personal', 1, 1, 4)->once()->andReturn(
        'Restarting FPM...',
    );

    $this->artisan('deploy:logs', ['site' => 'pestphp.com'])
        ->expectsOutput('  ▕ Restarting FPM...');
});

it('can not display the status when there is no deployments', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('deployments')->with('personal', 1, 1)->once()->andReturn(fakePaginator([]));

    $this->artisan('deploy:logs', ['site' => 1]);
})->throws('This site has not been deployed.');
