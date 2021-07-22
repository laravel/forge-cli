<?php

it('can display the php status running', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222', 'phpVersion' => 'php56'],
    );

    $this->remote->shouldReceive('exec')->andReturn([0]);

    $this->artisan('php:status')->expectsOutput('==> PHP 5.6 Is Up & Running');
});

it('can display the php status as inactive', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222', 'phpVersion' => 'php80'],
    );

    $this->remote->shouldReceive('exec')->andReturn([3]);

    $this->artisan('php:status');
})->throws('Service is not running.');

it('can not display the status when php is incorrect', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => 'php56'],
    );

    $this->artisan('php:status', ['version' => '2.0']);
})->throws('PHP version needs to be one of those values: 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0.');

it('can not display the status when there is no php', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => null],
    );

    $this->artisan('php:status');
})->throws('PHP is not installed in this server.');
