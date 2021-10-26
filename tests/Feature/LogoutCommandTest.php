<?php

it('logout users', function () {
    $this->artisan('logout')
        ->expectsOutput('==> Logged Out Successfully');

    expect($this->config->get('server'))->toBeNull();
    expect($this->config->get('token'))->toBeNull();
});
