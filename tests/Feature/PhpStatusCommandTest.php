<?php

use Laravel\Forge\Resources\Server;

it('can display the php status running', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222', 'phpVersion' => 'PHP 5.6']),
    );

    $this->remote->shouldReceive('exec')
        ->with('systemctl is-active --quiet php5.6-fpm')
        ->andReturn([0]);

    $this->artisan('php:status')
        ->expectsPromptsInfo('PHP 5.6 is up & running');
});

it('can display the php status as inactive', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222', 'phpVersion' => 'PHP 8.0']),
    );

    $this->remote->shouldReceive('exec')
        ->with('systemctl is-active --quiet php8.0-fpm')
        ->andReturn([3]);

    $this->artisan('php:status');
})->throws('Service is not running.');

it('can not display the status when php is incorrect', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => 'PHP 5.6']),
    );

    $this->artisan('php:status', ['version' => '2.0']);
})->throws('PHP version needs to be one of these values: 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5.');

it('can not display the status when there is no php', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => null]),
    );

    $this->artisan('php:status');
})->throws('PHP is not installed on this server.');
