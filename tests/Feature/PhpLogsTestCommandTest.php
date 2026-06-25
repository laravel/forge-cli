<?php

use Laravel\Forge\Resources\Server;

it('can retrieve logs from php', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => 'PHP 5.6']),
    );

    $this->client->shouldReceive('serverLog')
        ->with('personal', 1, 'php56')
        ->andReturn("   [00:01] FOO\n[00:02] BAR\n   ");

    $this->artisan('php:logs')
        ->expectsPromptsInfo('Retrieving the latest logs')
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can retrieve logs from a specific php version', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => 'PHP 8.0']),
    );

    $this->client->shouldReceive('serverLog')
        ->with('personal', 1, 'php81')
        ->andReturn("   tail: cannot open '/var/log/php8.1-fpm.log' for reading: No such file or directory\n   ");

    $this->artisan('php:logs', ['version' => '8.1'])
        ->expectsPromptsInfo('Retrieving the latest logs')
        ->expectsOutput("  ▕ tail: cannot open '/var/log/php8.1-fpm.log' for reading: No such file or directory");
});

it('can not retrieve logs when php version is incorrect', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => 'PHP 8.0']),
    );

    $this->artisan('php:logs', ['version' => '2.0']);
})->throws('PHP version needs to be one of these values: 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5.');

it('can not display the logs when there is no php', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => null]),
    );

    $this->artisan('php:logs');
})->throws('PHP is not installed on this server.');
