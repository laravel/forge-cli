<?php

it('can not retrieve a daemon status yet')
    ->artisan('daemon:status')
    ->throws('Checking a daemon\'s status is not yet supported');
