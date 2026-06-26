<?php

use Laravel\Forge\Resources\Organization;

it('allows to switch the organization with a menu', function () {
    $this->client->shouldReceive('organizations')->andReturn(fakePaginator([
        new Organization(['id' => '1', 'name' => 'Personal', 'slug' => 'personal']),
        new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme']),
    ]));

    $this->client->shouldReceive('organization')->andReturn(
        new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme']),
    );

    $this->artisan('organization:switch')
        ->expectsSearch(
            'Which organization would you like to switch to',
            answer: 'acme',
            search: 'acme',
            answers: ['acme' => 'Acme Inc'],
        )
        ->expectsPromptsInfo('Current organization changed successfully to Acme Inc');

    expect($this->config->get('organization'))->toBe('acme');
    expect($this->config->get('server'))->toBeNull();
});

it('allows to switch the organization with the slug as an argument', function () {
    $this->client->shouldReceive('organization')->with('acme')->andReturn(
        new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme']),
    );

    $this->artisan('organization:switch', ['organization' => 'acme'])
        ->expectsPromptsInfo('Current organization changed successfully to Acme Inc');

    expect($this->config->get('organization'))->toBe('acme');
    expect($this->config->get('server'))->toBeNull();
});
