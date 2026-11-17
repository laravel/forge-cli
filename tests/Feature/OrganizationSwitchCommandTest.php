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

it('uses the stored token of the organization', function () {
    trackApiToken($this);

    $this->config->set('tokens', [
        'personal' => 'personal-token',
        'acme' => 'acme-token',
    ]);

    $this->client->shouldReceive('organization')->with('acme')->andReturnUsing(function () {
        expect($this->apiToken)->toBe('acme-token');

        return new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme']);
    });

    $this->artisan('organization:switch', ['organization' => 'acme'])
        ->expectsPromptsInfo('Current organization changed successfully to Acme Inc');

    expect($this->config->get('organization'))->toBe('acme');
    expect($this->forge->token())->toBe('acme-token');
});

it('finds the token that can access the organization', function () {
    trackApiToken($this);

    $this->config->set('token', 'acme-token');
    $this->config->set('tokens', ['personal' => 'personal-token']);

    $this->client->shouldReceive('organizations')->andReturnUsing(fn () => fakePaginator(match ($this->apiToken) {
        'personal-token' => [new Organization(['id' => '1', 'name' => 'Personal', 'slug' => 'personal'])],
        'acme-token' => [
            new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme']),
            new Organization(['id' => '3', 'name' => 'Acme Labs', 'slug' => 'acme-labs']),
        ],
    }));

    $this->client->shouldReceive('organization')->with('acme-labs')->andReturnUsing(function () {
        expect($this->apiToken)->toBe('acme-token');

        return new Organization(['id' => '3', 'name' => 'Acme Labs', 'slug' => 'acme-labs']);
    });

    $this->artisan('organization:switch', ['organization' => 'acme-labs'])
        ->expectsPromptsInfo('Current organization changed successfully to Acme Labs');

    expect($this->config->get('organization'))->toBe('acme-labs');
    expect($this->forge->token())->toBe('acme-token');
});

it('remembers the token of an organization found through another token', function () {
    trackApiToken($this);

    $this->config->set('token', 'personal-token');
    $this->config->set('tokens', ['personal' => 'personal-token', 'acme' => 'acme-token']);

    $this->client->shouldReceive('organizations')->andReturnUsing(fn () => fakePaginator(match ($this->apiToken) {
        'personal-token' => [new Organization(['id' => '1', 'name' => 'Personal', 'slug' => 'personal'])],
        'acme-token' => [
            new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme']),
            new Organization(['id' => '3', 'name' => 'Acme Labs', 'slug' => 'acme-labs']),
        ],
    }));

    $this->client->shouldReceive('organization')->with('acme-labs')->andReturn(
        new Organization(['id' => '3', 'name' => 'Acme Labs', 'slug' => 'acme-labs']),
    );

    $this->artisan('organization:switch', ['organization' => 'acme-labs']);

    expect($this->config->get('tokens'))->toBe([
        'acme-labs' => 'acme-token',
        'personal' => 'personal-token',
        'acme' => 'acme-token',
    ]);
});
