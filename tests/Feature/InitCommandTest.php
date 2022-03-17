<?php

use Illuminate\Support\Facades\File;

it('can initialize inside of a directory', function () {
    File::shouldReceive('isDirectory')
        ->once()
        ->andReturnFalse();

    File::shouldReceive('makeDirectory')
        ->once()
        ->with(getcwd() . '/.laravel-forge')
        ->andReturnTrue();

    File::shouldReceive('isWritable')
        ->once()
        ->andReturnTrue();

    File::shouldReceive('append')
        ->once()
        ->with(getcwd() . '/.gitignore', '.laravel-forge' . PHP_EOL)
        ->andReturn(15);

    $this->artisan('init')
        ->expectsOutput('==> Initialized Successfully');
});
