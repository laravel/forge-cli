<?php

it('can restart mysql databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql'],
    );

    $this->client->shouldReceive('rebootMysql');

    $this->artisan('database:restart')
        ->expectsConfirmation(
            'The database may become unavailable while the <comment>[MySQL]</comment> service restarts. Continue?',
            'yes',
        )->expectsOutput('==> Database Restart Initiated Successfully');
});

it('can restart postgres databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'postgres'],
    );

    $this->client->shouldReceive('rebootPostgres');

    $this->artisan('database:restart')
        ->expectsConfirmation(
            'The database may become unavailable while the <comment>[PostgreSQL]</comment> service restarts. Continue?',
            'yes'
        )->expectsOutput('==> Database Restart Initiated Successfully');
});

it('can not restart when there is no database', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => null],
    );

    $this->artisan('database:restart');
})->throws('No databases installed on this server.');

it('can not restart unknown databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'nitro'],
    );

    $this->artisan('database:restart');
})->throws('Restarting [nitro] databases is not supported.');
