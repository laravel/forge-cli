<?php

beforeEach(function () {
    $this->config->flush();
});

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

    expect($this->config->get('token'))->toBe('123123123');
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
