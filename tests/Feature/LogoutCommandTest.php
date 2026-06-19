<?php

it('logout users', function () {
    $this->artisan('logout')
        ->expectsPromptsInfo('Logged out successfully');

    expect($this->config->get('server'))->toBeNull();
    expect($this->config->get('token'))->toBeNull();
});
