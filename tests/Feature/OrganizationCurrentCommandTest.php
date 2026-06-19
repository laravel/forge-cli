<?php

use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Resources\Organization;

it('gets the current organization', function () {
    $this->client->shouldReceive('organization')->with('acme')->andReturn(
        new Organization(['name' => 'Acme Inc', 'slug' => 'acme']),
    );

    $this->config->set('organization', 'acme');

    $this->artisan('organization:current')
        ->expectsPromptsInfo('You are currently within the Acme Inc (acme) organization context.');
});

it('can be run via the org:current alias', function () {
    $this->client->shouldReceive('organization')->with('acme')->andReturn(
        new Organization(['name' => 'Acme Inc', 'slug' => 'acme']),
    );

    $this->config->set('organization', 'acme');

    $this->artisan('org:current')
        ->expectsPromptsInfo('You are currently within the Acme Inc (acme) organization context.');
});

it('fails when no organization has been selected', function () {
    $this->artisan('organization:current');
})->throws('You have not selected an organization. Use the \'organization:switch\' command.');

it('may fail if the current organization no longer exists', function () {
    $this->client->shouldReceive('organization')->with('acme')->andThrow(
        new NotFoundException('The resource you are looking for could not be found.'),
    );

    $this->config->set('organization', 'acme');

    $this->artisan('organization:current');
})->throws('The resource you are looking for could not be found.');
