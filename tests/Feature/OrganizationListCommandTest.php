<?php

use Laravel\Forge\Resources\Organization;

it('displays the list of organizations', function () {
    $this->client->shouldReceive('organizations')->andReturn(fakePaginator([
        new Organization(['id' => '1', 'name' => 'Personal', 'slug' => 'personal']),
        new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme']),
    ]));

    $this->artisan('organization:list')
        ->expectsPromptsTable(['Name', 'Slug'], [
            ['Personal', 'personal'],
            ['Acme Inc', 'acme'],
        ]);
});

it('can be run via the org:list alias', function () {
    $this->client->shouldReceive('organizations')->andReturn(fakePaginator([
        new Organization(['id' => '1', 'name' => 'Personal', 'slug' => 'personal']),
    ]));

    $this->artisan('org:list')
        ->expectsPromptsTable(['Name', 'Slug'], [
            ['Personal', 'personal'],
        ]);
});

it('displays the organizations of all stored tokens', function () {
    trackApiToken($this);

    $this->config->set('tokens', [
        'personal' => 'personal-token',
        'acme' => 'acme-token',
    ]);

    $this->client->shouldReceive('organizations')->andReturnUsing(fn () => fakePaginator(match ($this->apiToken) {
        'personal-token' => [new Organization(['id' => '1', 'name' => 'Personal', 'slug' => 'personal'])],
        'acme-token' => [new Organization(['id' => '2', 'name' => 'Acme Inc', 'slug' => 'acme'])],
        '123123213' => [new Organization(['id' => '1', 'name' => 'Personal', 'slug' => 'personal'])],
    }));

    $this->artisan('organization:list')
        ->expectsPromptsTable(['Name', 'Slug'], [
            ['Personal', 'personal'],
            ['Acme Inc', 'acme'],
        ]);
});
