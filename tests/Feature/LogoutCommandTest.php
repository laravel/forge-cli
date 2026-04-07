<?php

it('logout users', function () {
    $this->artisan('logout')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Are You Sure You Want To Log Out? This Will Remove Your Stored API Token And Configuration</>', 'yes')
        ->expectsOutput('==> Logged Out Successfully');

    expect($this->config->get('server'))->toBeNull();
    expect($this->config->get('token'))->toBeNull();
});
