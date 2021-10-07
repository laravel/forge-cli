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
        ->expectsTable(['   ID', '   Name', '   PHP'], [
            ['id' => '   1', 'name' => '   production.com (production, php 5.6)', 'phpVersion' => '   5.6'],
            ['id' => '   2', 'name' => '   staging.com', 'phpVersion' => '   None'],
            ['id' => '   3', 'name' => '   acceptance.com (acceptance)', 'phpVersion' => '   None'],
        ], 'compact');
});

it('do not display archived servers', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->andReturn([
        new Site(['id' => 1, 'name' => 'production.com', 'phpVersion' => 'php56', 'tags' => []]),
        new Site(['id' => 2, 'name' => 'staging.com', 'phpVersion' => null, 'tags' => []]),
        new Site(['id' => 3, 'name' => 'archived.com', 'phpVersion' => 'php80', 'revoked' => true, 'tags' => []]),
        new Site(['id' => 4, 'name' => 'non-archived.com', 'phpVersion' => null, 'revoked' => false, 'tags' => []]),
    ]);

    $this->artisan('site:list')
        ->expectsTable(['   ID', '   Name', '   PHP'], [
            ['id' => '   1', 'name' => '   production.com', 'phpVersion' => '   5.6'],
            ['id' => '   2', 'name' => '   staging.com', 'phpVersion' => '   None'],
            ['id' => '   4', 'name' => '   non-archived.com', 'phpVersion' => '   None'],
        ], 'compact');
});
