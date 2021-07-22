<?php

use App\Support\Panic;

it('aborts', function () {
    Panic::abort('Foo');
})->throws('An unexpected error occured.');
