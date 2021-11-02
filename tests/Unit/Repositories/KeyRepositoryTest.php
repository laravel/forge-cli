<?php

use App\Repositories\KeyRepository;

it('creates keys', function () {
    $keys = new KeyRepository('/tmp');

    File::shouldReceive('exists')->once()->with('/tmp/mysshkey')->andReturn(false);
    File::shouldReceive('exists')->once()->with('/tmp/mysshkey.pub')->andReturn(false);

    File::shouldReceive('put')->once()->with('/tmp/mysshkey', Mockery::any());
    File::shouldReceive('chmod')->once()->with('/tmp/mysshkey', 0600);

    File::shouldReceive('put')->once()->with('/tmp/mysshkey.pub', Mockery::any());

    expect($keys->create('mysshkey'))->sequence(
        'mysshkey.pub',
        function ($key) {
            $key->toContain(
                'ssh-ed25519',
                'forge-cli-generated-key'
            );
        }
    );
});

it('do not overrides existing keys', function () {
    $keys = new KeyRepository('/tmp');

    File::shouldReceive('exists')->once()->with('/tmp/mysshkey')->andReturn(false);
    File::shouldReceive('exists')->once()->with('/tmp/mysshkey.pub')->andReturn(true);

    $keys->create('mysshkey');
})->throws('The name has already been taken.');

it('gets keys', function () {
    $keys = new KeyRepository('/tmp');

    File::shouldReceive('exists')->once()->with('/home/driesvints/.ssh/id.pub')->andReturn(
        true
    );

    File::shouldReceive('get')->once()->with('/home/driesvints/.ssh/id.pub')->andReturn(
        'My Key Content'
    );

    expect($keys->get('/home/driesvints/.ssh/id.pub'))->toBe([
        'id.pub',
        'My Key Content',
    ]);
});
