<?php

it('can retrieve deployment logs from sites with an menu', function () {
    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('siteDeployments')->with(1, 2)->once()->andReturn([
        ['id' => 3],
    ]);

    $this->client->shouldReceive('siteDeploymentOutput')->with(1, 2, 3)->once()->andReturn(
        'Restarting FPM...',
    );

    $this->artisan('deploy:logs')
        ->expectsChoice('Which site would you like to retrieve the deployment logs from?', 'something.com', [
            'pestphp.com', 'something.com',
        ])->expectsOutput('  ▕ Restarting FPM...');
});

it('can retrieve deployment logs from sites with an option', function () {
    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('siteDeployments')->with(1, 1)->once()->andReturn([
        ['id' => 4],
    ]);

    $this->client->shouldReceive('siteDeploymentOutput')->with(1, 1, 4)->once()->andReturn(
        'Restarting FPM...',
    );

    $this->artisan('deploy:logs', ['--id' => 1])
        ->expectsOutput('  ▕ Restarting FPM...');
});

it('can not display the status when there is no deployments', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('siteDeployments')->with(1, 1)->once()->andReturn([]);

    $this->artisan('deploy:logs', ['--id' => 1]);
})->throws('No deployments have been made in this site.');
