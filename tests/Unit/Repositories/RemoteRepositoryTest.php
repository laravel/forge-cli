<?php

use App\Repositories\RemoteRepository;

it('ensure current server', function () {
    (new RemoteRepository('foo'))->exec('bar');
})->throws('Current server unresolvable.');
