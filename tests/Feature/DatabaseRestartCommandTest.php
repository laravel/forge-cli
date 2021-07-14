<?php

it('can restart mysql databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql'],
    );

    $this->client->shouldReceive('rebootMysql');

    $this->artisan('database:restart')
        ->expectsConfirmation(
            'While the <comment>[MySQL]</comment> service restarts, the database will be unavailable. Wish to proceed?',
            'yes',
        )->expectsOutput('Database restart initiated successfully.');
});

it('can restart postgres databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'postgres'],
    );

    $this->client->shouldReceive('rebootPostgres');

    $this->artisan('database:restart')
        ->expectsConfirmation(
            'While the <comment>[PostgreSQL]</comment> service restarts, the database will be unavailable. Wish to proceed?',
            'yes'
        )->expectsOutput('Database restart initiated successfully.');
});

it('can not restart unknown databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'nitro'],
    );

    $this->artisan('database:restart');
})->throws('The database type [nitro] is not restartable.');
