<?php

it('can deploy sites with an menu', function () {
    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('site')->twice()->with(1, 1)->andReturn(
        (object) ['id' => 1, 'name' => 'pestphp.com', 'deploymentStatus' => null],
    );

    $this->client->shouldReceive('deploySite')->with(1, 1, false)->once()->andReturn(null);

    $this->client->shouldReceive('events')->with(1)->once()->andReturn([
        (object) ['id' => 3, 'description' => 'Deploying Pushed Code (pestphp.com).'],
        (object) ['id' => 2, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 1, 'description' => 'Deploying Pushed Code (pestphp.com).'],
    ]);

    $this->client->shouldReceive('siteDeployments')->with(1, 1)->once()->andReturn([
        ['id' => 3],
    ]);

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-3.output'
    )->once()->andReturn([0, [
        'Installing composer dependencies...',
    ]]);

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-3.output'
    )->once()->andReturn([0, [
        'Installing composer dependencies...',
        'Restarting FPM...',
    ]]);

    $this->client->shouldReceive('siteDeployment')->with(1, 1, 3)->once()->andReturn(
        (object) ['id' => 3, 'status' => 'finished', 'started_at' => '2021-07-20 12:50:01', 'ended_at' => '2021-07-20 12:50:09'],
    );

    $this->artisan('deploy')
        ->expectsChoice('Which site would you like to deploy', 'pestphp.com', [
            'pestphp.com', 'something.com',
        ])->expectsOutput('==> Queuing Deployment')
            ->expectsOutput('==> Waiting For Deployment To Start')
            ->expectsOutput('==> Deploying')
            ->expectsOutput('  ▕ Installing composer dependencies...')
            ->expectsOutput('  ▕ Restarting FPM...')
            ->expectsOutput('==> Site Deployed Successfully. (8s)');
});

it('can deploy sites with an option', function () {
    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('site')->twice()->with(1, 2)->andReturn(
        (object) ['id' => 2, 'name' => 'something.com', 'deploymentStatus' => null],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('deploySite')->with(1, 2, false)->once()->andReturn(null);

    $this->client->shouldReceive('events')->with(1)->once()->andReturn([
        (object) ['id' => 3, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 2, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 1, 'description' => 'Deploying Pushed Code (pestphp.com).'],
    ]);

    $this->client->shouldReceive('siteDeployments')->with(1, 2)->once()->andReturn([
        ['id' => 3],
    ]);

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-3.output'
    )->once()->andReturn([0, [
        'Installing composer dependencies...',
    ]]);

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-3.output'
    )->once()->andReturn([0, [
        'Installing composer dependencies...',
        'Restarting FPM...',
    ]]);

    $this->client->shouldReceive('siteDeployment')->with(1, 2, 3)->once()->andReturn(
        (object) ['id' => 3, 'status' => 'finished', 'started_at' => '2021-07-20 12:50:01', 'ended_at' => '2021-07-20 12:50:09'],
    );

    $this->artisan('deploy', ['site' => 2])
        ->expectsOutput('==> Queuing Deployment')
        ->expectsOutput('==> Waiting For Deployment To Start')
        ->expectsOutput('==> Deploying')
        ->expectsOutput('  ▕ Installing composer dependencies...')
        ->expectsOutput('  ▕ Restarting FPM...')
        ->expectsOutput('==> Site Deployed Successfully. (8s)');
});

it('can not deploy sites that are already deploying', function () {
    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 2)->andReturn(
        (object) ['id' => 2, 'name' => 'something.com', 'deploymentStatus' => 'queued'],
    );

    $this->artisan('deploy', ['site' => 'something.com']);
})->throws('This site is already deploying.');

it('handles deployment failures', function () {
    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->twice()->with(1, 2)->andReturn(
        (object) ['id' => 2, 'name' => 'something.com', 'deploymentStatus' => null],
    );

    $this->client->shouldReceive('deploySite')->with(1, 2, false)->once()->andReturn(null);

    $this->client->shouldReceive('events')->with(1)->once()->andReturn([
        (object) ['id' => 3, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 2, 'description' => 'Deploying Pushed Code (something.com).'],
        (object) ['id' => 1, 'description' => 'Deploying Pushed Code (pestphp.com).'],
    ]);

    $this->client->shouldReceive('siteDeployments')->with(1, 2)->once()->andReturn([
        ['id' => 3],
    ]);

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-3.output'
    )->once()->andReturn([0, ['Installing composer dependencies...']]);

    $this->remote->shouldReceive('exec')->with(
        'cat /home/forge/.forge/provision-3.output'
    )->once()->andReturn([0, [
        'Installing composer dependencies...',
        'Restarting FPM failed...',
    ]]);

    $this->client->shouldReceive('siteDeployment')->with(1, 2, 3)->once()->andReturn(
        (object) ['id' => 3, 'status' => 'failed', 'started_at' => '2021-07-20 12:50:01', 'ended_at' => '2021-07-20 12:50:09'],
    );

    $this->artisan('deploy', ['site' => 2]);
})->throws('The deployment failed.');
