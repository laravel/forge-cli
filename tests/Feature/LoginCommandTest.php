<?php

it('authenticates users', function () {
    $this->client->shouldReceive('user')->andReturn((object) [
        'email' => 'nuno@laravel.com',
    ]);

    $this->client->shouldReceive('servers')->andReturn([
        (object) ['id' => 1],
    ]);

    $this->artisan('login')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Please Enter Your Forge API Token</>', '123123213')
        ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');
});

it('authenticates users with token', function () {
    $this->client->shouldReceive('user')->andReturn((object) [
        'email' => 'nuno@laravel.com',
    ]);

    $this->client->shouldReceive('servers')->andReturn([
        (object) ['id' => 1],
    ]);

    $this->artisan('login --token 123123123')
        ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');
});

it('sets current server', function () {
    $this->client->shouldReceive('user')->andReturn((object) [
        'email' => 'nuno@laravel.com',
    ]);

    $this->client->shouldReceive('servers')->andReturn([
        (object) ['id' => 1],
    ]);

    $this->artisan('login')->expectsQuestion('<fg=yellow>‣</> <options=bold>Please Enter Your Forge API Token</>', '123123213');

    expect($this->config->get('server'))->toBe(1);
});

it('ensures at least one server', function () {
    $this->client->shouldReceive('user')->andReturn((object) [
        'email' => 'nuno@laravel.com',
    ]);

    $this->client->shouldReceive('servers')->andReturn([]);

    $this->artisan('login')->expectsQuestion('<fg=yellow>‣</> <options=bold>Please Enter Your Forge API Token</>', '123123213');
})->throws('Please create a server first.');

it('may consider the api token invalid', function () {
    $this->client->shouldReceive('user')->andThrow(
        new Exception('Unauthorized')
    );

    $this->artisan('login')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Please Enter Your Forge API Token</>', '123123213');
})->throws('Your API Token is invalid.');
