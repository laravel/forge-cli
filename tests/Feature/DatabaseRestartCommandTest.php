<?php

it('can restart mysql databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql'],
    );

    $this->client->shouldReceive('rebootMysql');

    $this->artisan('database:restart')
        ->expectsConfirmation(
            'While the <comment>[MySQL]</comment> service restarts, the database may become unavailable. Wish to proceed?',
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
            'While the <comment>[PostgreSQL]</comment> service restarts, the database may become unavailable. Wish to proceed?',
            'yes'
        )->expectsOutput('==> Database Restart Initiated Successfully');
});

it('can not restart when there is no database', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => null],
    );

    $this->artisan('database:restart');
})->throws('No databases installed in this server.');

it('can not restart unknown databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'nitro'],
    );

    $this->artisan('database:restart');
})->throws('Restarting [nitro] databases is not supported.');
