<?php

it('logout users', function () {
    $this->artisan('logout')
        ->expectsPromptsInfo('Logged out successfully');

    expect($this->config->get('server'))->toBeNull();
    expect($this->config->get('token'))->toBeNull();
});

it('only removes the token of the current organization', function () {
    $this->config->set('token', 'acme-token');
    $this->config->set('organization', 'acme');
    $this->config->set('tokens', [
        'personal' => 'personal-token',
        'acme' => 'acme-token',
        'acme-labs' => 'acme-token',
    ]);

    $this->artisan('logout')
        ->expectsPromptsInfo('Logged out successfully');

    expect($this->config->get('tokens'))->toBe(['personal' => 'personal-token']);
    expect($this->config->get('token'))->toBeNull();
    expect($this->config->get('organization'))->toBeNull();
    expect($this->config->get('server'))->toBeNull();
});

it('removes the tokens of all organizations', function () {
    $this->config->set('tokens', [
        'personal' => 'personal-token',
        'acme' => 'acme-token',
    ]);

    $this->artisan('logout --all')
        ->expectsPromptsInfo('Logged out successfully');

    expect($this->config->get('tokens'))->toBeNull();
    expect($this->config->get('token'))->toBeNull();
    expect($this->config->get('organization'))->toBeNull();
});
