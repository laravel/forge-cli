<?php

it('can display the list of sites', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'production.com', 'phpVersion' => 'php56'],
        (object) ['id' => 2, 'name' => 'staging.com', 'phpVersion' => null],
    ]);

    $this->artisan('site:list')
        ->expectsTable(['   ID', '   Name', '   PHP'], [
            ['id' => '   1', 'name' => '   production.com', 'phpVersion' => '   5.6'],
            ['id' => '   2', 'name' => '   staging.com', 'phpVersion' => '   None'],
        ], 'compact');
});

it('do not display archived servers', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'production.com', 'phpVersion' => 'php56'],
        (object) ['id' => 2, 'name' => 'staging.com', 'phpVersion' => null],
        (object) ['id' => 3, 'name' => 'archived.com', 'phpVersion' => 'php80', 'revoked' => true],
        (object) ['id' => 4, 'name' => 'non-archived.com', 'phpVersion' => null, 'revoked' => false],
    ]);

    $this->artisan('site:list')
        ->expectsTable(['   ID', '   Name', '   PHP'], [
            ['id' => '   1', 'name' => '   production.com', 'phpVersion' => '   5.6'],
            ['id' => '   2', 'name' => '   staging.com', 'phpVersion' => '   None'],
            ['id' => '   4', 'name' => '   non-archived.com', 'phpVersion' => '   None'],
        ], 'compact');
});
