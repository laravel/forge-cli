<?php

it('can restart php', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => 'php80'],
    );

    $this->client->shouldReceive('rebootPhp');

    $this->artisan('php:restart')
        ->expectsConfirmation(
            'The sites may become unavailable while the <comment>[PHP 8.0]</comment> service restarts. Continue?',
            'yes',
        )->expectsOutput('==> PHP 8.0 Restart Initiated Successfully.');
});

it('can restart a specific php version', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => 'php56'],
    );

    $this->client->shouldReceive('rebootPhp');

    $this->artisan('php:restart', [
        'version' => '7.4',
    ])->expectsConfirmation(
        'The sites may become unavailable while the <comment>[PHP 7.4]</comment> service restarts. Continue?',
        'yes',
    )->expectsOutput('==> PHP 7.4 Restart Initiated Successfully.');
});

it('can restart php when php version is incorrect', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => 'php80'],
    );

    $this->artisan('php:restart', ['version' => '2.0']);
})->throws('PHP version needs to be one of these values: 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0.');

it('can not restart php when there is no php', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => null],
    );

    $this->artisan('php:status');
})->throws('PHP is not installed on this server.');
