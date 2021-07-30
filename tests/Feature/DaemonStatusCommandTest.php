<?php

it('can not retrieve a daemon status yet')
    ->artisan('daemon:status')
    ->throws('Checking a daemon status is not yet supported');
