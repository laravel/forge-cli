<?php

use Laravel\Forge\Exceptions\NotFoundException;

it('gets current server', function () {
    $this->client->shouldReceive('server')->with(1)->andReturn((object) [
        'name' => 'production',
    ]);

    $this->config->set('server', 1);

    $this->artisan('server:current')
        ->expectsOutput('You are currently within the [production] server context.');
});

it('current server gets deleted', function () {
    $this->client->shouldReceive('server')->with(1)->andThrow(
        new NotFoundException('The resource you are looking for could not be found.'),
    );

    $this->config->set('server', 1);

    $this->artisan('server:current');
})->throws('The resource you are looking for could not be found.');
