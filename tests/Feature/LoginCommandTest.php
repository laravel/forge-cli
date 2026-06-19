<?php

use Laravel\Forge\Resources\User;

beforeEach(function () {
    $this->config->flush();
});

it('authenticates users', function () {
    $this->client->shouldReceive('user')->andReturn(
        new User(['email' => 'nuno@laravel.com']),
    );

    $this->artisan('login')
        ->expectsQuestion('Please enter your Forge API token', '123123213')
        ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');

    expect($this->config->get('token'))->toBe('123123213');
});

it('authenticates users with token', function () {
    $this->client->shouldReceive('user')->andReturn(
        new User(['email' => 'nuno@laravel.com']),
    );

    $this->artisan('login --token 123123123')
        ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');

    expect($this->config->get('token'))->toBe('123123123');
});
