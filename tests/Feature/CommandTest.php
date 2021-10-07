<?php

use App\Commands\Command;
use Laravel\Forge\Resources\Server;

beforeEach(function () {
    $this->client->shouldReceive('servers')->andReturn([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000']),
    ]);
});

afterEach(function () {
    Command::resolveLatestVersionUsing(null);
});

it('warns the user about the latest unstable versions', function () {
    config()->set('app.version', '0.2.1');

    Command::resolveLatestVersionUsing(function () {
        return 'v0.2.2';
    });

    $this->artisan('server:list')
        ->expectsOutput('==> You Are Using An Outdated Version [v0.2.1] Of Forge CLI. Please Update To [v0.2.2].');
});

it('warns the user about the latest stable versions', function () {
    config()->set('app.version', '0.2.1');

    Command::resolveLatestVersionUsing(function () {
        return 'v1.0.0';
    });

    $this->artisan('server:list')
        ->expectsOutput('==> You Are Using An Outdated Version [v0.2.1] Of Forge CLI. Please Update To [v1.0.0].');
});

it('do not warns the user if the version is already up-to-date', function () {
    config()->set('app.version', '1.0.1');

    Command::resolveLatestVersionUsing(function () {
        return 'v1.0.1';
    });

    $this->artisan('server:list')
        ->doesntExpectOutput('==> You Are Using An Outdated Version [v1.0.1] Of Forge CLI. Please Update To [v1.0.1].');
});
