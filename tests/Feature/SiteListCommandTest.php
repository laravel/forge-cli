<?php

use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can display the list of sites', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'production.com', 'phpVersion' => 'PHP 8.4']),
        new Site(['id' => 2, 'name' => 'staging.com', 'phpVersion' => null]),
        new Site(['id' => 3, 'name' => 'acceptance.com', 'phpVersion' => 'PHP 8.0']),
    ]));

    $this->artisan('site:list')
        ->expectsPromptsTable(['ID', 'Name', 'PHP'], [
            [1, 'production.com', '8.4'],
            [2, 'staging.com', 'None'],
            [3, 'acceptance.com', '8.0'],
        ]);
});

it('warns when there are no sites', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1]),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->andReturn(fakePaginator([]));

    $this->artisan('site:list')
        ->expectsPromptsWarning('No sites found.');
});

it('aborts when no server is selected', function () {
    $this->config->forget('server');

    $this->artisan('site:list');
})->throws('You have not selected a server. Use the \'server:switch\' command.');
