<?php

use App\Repositories\KeyRepository;

it('creates keys', function () {
    $keys = new KeyRepository('/tmp');

    File::shouldReceive('put')->once()->with('/tmp/mohamed_rsa', Mockery::any());
    File::shouldReceive('put')->once()->with('/tmp/mohamed_rsa.pub', Mockery::any());

    File::shouldReceive('exists')->once()->with('/tmp/mohamed_rsa')->andReturn(false);
    File::shouldReceive('exists')->once()->with('/tmp/mohamed_rsa.pub')->andReturn(false);

    expect($keys->create('mohamed'))->sequence(
        'mohamed_rsa.pub',
        function ($key) {
            $key->not->toBeEmpty()->toBeString();
        }
    );
});

it('do not overrides existing keys', function () {
    $keys = new KeyRepository('/tmp');

    File::shouldReceive('exists')->once()->with('/tmp/mohamed_rsa')->andReturn(false);
    File::shouldReceive('exists')->once()->with('/tmp/mohamed_rsa.pub')->andReturn(true);

    $keys->create('mohamed');
})->throws('The name has already been taken.');

it('gets keys', function () {
    $keys = new KeyRepository('/tmp');

    File::shouldReceive('exists')->once()->with('/home/driesvints/.ssh/id_rsa.pub')->andReturn(
        true
    );

    File::shouldReceive('get')->once()->with('/home/driesvints/.ssh/id_rsa.pub')->andReturn(
        'My Key Content'
    );

    expect($keys->get('/home/driesvints/.ssh/id_rsa.pub'))->toBe([
        'id_rsa.pub',
        'My Key Content',
    ]);
});
