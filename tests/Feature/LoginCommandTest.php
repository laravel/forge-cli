<?php

it('authenticate users', function () {
    $this->artisan('login')
        ->expectsQuestion('Email Address', 'nuno@laravel.com')
        ->expectsQuestion('Password', '123123123')
        ->expectsOutput("Your are now logged as [nuno@laravel.com].")
        ->assertExitCode(0);
});
