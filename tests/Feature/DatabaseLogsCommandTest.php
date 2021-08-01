<?php

it('can retrieve logs from databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql'],
    );

    $this->client->shouldReceive('logs')->andReturn((object) [
        'content' => "   [00:01] FOO\n[00:02] BAR\n   ",
    ]);

    $this->artisan('database:logs')
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can not retrieve logs when there is no databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => null],
    );

    $this->artisan('database:logs');
})->throws('No databases installed on this server.');

it('can not retrieve logs from unknown databases', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'nitro'],
    );

    $this->artisan('database:logs');
})->throws('Retrieving logs from [nitro] databases is not supported.');
