<?php

it('can retrieve logs from php', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => 'php56'],
    );

    $this->client->shouldReceive('logs')
        ->with(Mockery::any(), 'php56')
        ->andReturn((object) [
            'content' => "   [00:01] FOO\n[00:02] BAR\n   ",
        ]);

    $this->artisan('php:logs')
        ->expectsOutput('[00:01] FOO')
        ->expectsOutput('[00:02] BAR');
});

it('can retrieve logs from a specific php version', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'phpVersion' => '8.0'],
    );

    $this->client->shouldReceive('logs')
        ->with(Mockery::any(), 'php80')
        ->andReturn((object) [
            'content' => "   tail: cannot open '/var/log/php8.0-fpm.log' for reading: No such file or directory\n   ",
        ]);

    $this->artisan('php:logs', ['--type' => '8.0'])
        ->expectsOutput("tail: cannot open '/var/log/php8.0-fpm.log' for reading: No such file or directory");
});

it('can not retrieve logs from php versions that do not exist', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->artisan('php:logs', ['--type' => '2.0']);
})->throws('PHP version needs to be one of those values: 5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0.');
