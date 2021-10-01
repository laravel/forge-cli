<?php

use Laravel\Forge\Resources\Site;

it('can display the list of sites', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->andReturn([
        new Site(['id' => 1, 'name' => 'production.com', 'phpVersion' => 'php56', 'tags' => [['name' => 'production'], ['name' => 'php 5.6']]]),
        new Site(['id' => 2, 'name' => 'staging.com', 'phpVersion' => null, 'tags' => []]),
        new Site(['id' => 3, 'name' => 'acceptance.com', 'phpVersion' => null, 'tags' => [['name' => 'acceptance']]]),
    ]);

    $this->artisan('site:list')
        ->expectsTable(['   ID', '   Name', '   PHP', '   Tags'], [
            ['id' => '   1', 'name' => '   production.com', 'phpVersion' => '   5.6', 'tags' => '   production, php 5.6'],
            ['id' => '   2', 'name' => '   staging.com', 'phpVersion' => '   None', 'tags' => '   '],
            ['id' => '   3', 'name' => '   acceptance.com', 'phpVersion' => '   None', 'tags' => '   acceptance'],
        ], 'compact');
});

it('do not display archived servers', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->andReturn([
        new Site(['id' => 1, 'name' => 'production.com', 'phpVersion' => 'php56']),
        new Site(['id' => 2, 'name' => 'staging.com', 'phpVersion' => null]),
        new Site(['id' => 3, 'name' => 'archived.com', 'phpVersion' => 'php80', 'revoked' => true]),
        new Site(['id' => 4, 'name' => 'non-archived.com', 'phpVersion' => null, 'revoked' => false]),
    ]);

    $this->artisan('site:list')
        ->expectsTable(['   ID', '   Name', '   PHP', '   Tags'], [
            ['id' => '   1', 'name' => '   production.com', 'phpVersion' => '   5.6', 'tags' => ''],
            ['id' => '   2', 'name' => '   staging.com', 'phpVersion' => '   None', 'tags' => ''],
            ['id' => '   4', 'name' => '   non-archived.com', 'phpVersion' => '   None', 'tags' => ''],
        ], 'compact');
});
