<?php

use Laravel\Forge\Resources\Server;

it('can restart php', function () {
    $server = new Server([
        'id' => 1,
        'name' => 'production',
        'organization_slug' => 'personal',
        'phpVersion' => 'PHP 8.0',
    ], $this->client);

    $this->client->shouldReceive('performPHPAction')
        ->with('personal', 1, ['action' => 'reboot', 'version' => 'php80'])
        ->once();

    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        $server,
    );

    $this->artisan('php:restart')
        ->expectsConfirmation(
            'The sites may become unavailable while the PHP 8.0 service restarts. Continue?',
            'yes',
        )->expectsPromptsInfo('PHP 8.0 restart initiated successfully.');
});

it('can restart a specific php version', function () {
    $server = new Server([
        'id' => 1,
        'name' => 'production',
        'organization_slug' => 'personal',
        'phpVersion' => 'PHP 5.6',
    ], $this->client);

    $this->client->shouldReceive('performPHPAction')
        ->with('personal', 1, ['action' => 'reboot', 'version' => 'php74'])
        ->once();

    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        $server,
    );

    $this->artisan('php:restart', [
        'version' => '7.4',
    ])->expectsConfirmation(
        'The sites may become unavailable while the PHP 7.4 service restarts. Continue?',
        'yes',
    )->expectsPromptsInfo('PHP 7.4 restart initiated successfully.');
});

it('can restart php when php version is incorrect', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => 'PHP 8.0']),
    );

    $this->artisan('php:restart', ['version' => '2.0']);
})->throws('PHP version needs to be one of these values: 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5.');

it('can not restart php when there is no php', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'phpVersion' => null]),
    );

    $this->artisan('php:restart');
})->throws('PHP is not installed on this server.');
