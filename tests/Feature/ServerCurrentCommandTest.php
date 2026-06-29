<?php

use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Resources\Server;

it('gets current server', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['name' => 'production']),
    );

    $this->config->set('server', 1);

    $this->artisan('server:current')
        ->expectsPromptsInfo('You are currently within the production server context.');
});

it('fails when no server has been selected', function () {
    $this->config->set('server', null);

    $this->artisan('server:current');
})->throws('You have not selected a server. Use the \'server:switch\' command.');

it('may fail if current server no longer exists', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andThrow(
        new NotFoundException('The resource you are looking for could not be found.'),
    );

    $this->config->set('server', 1);

    $this->artisan('server:current');
})->throws('The resource you are looking for could not be found.');
