<?php

use App\Exceptions\UnauthorizedException;

it('ensures api token', function () {
    $this->config->flush();

    $this->forge->servers();
})->throws(UnauthorizedException::class, 'Please authenticate using the \'login\' command before proceeding.');
