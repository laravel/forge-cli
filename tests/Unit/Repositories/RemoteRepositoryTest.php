<?php

use App\Repositories\ConfigRepository;
use App\Repositories\RemoteRepository;

test('ensures current server', function () {
    (new RemoteRepository('foo', resolve(ConfigRepository::class)))->exec('bar');
})->throws('Current server unresolvable.');

test('exec removes sanitizable output', function () {
    $remote = new LocalRepository(
        'manpath: can\'t set the locale'
    );

    $command = collect([
        'manpath: can\'t set the locale',
        '[00:01] FOO',
        '[00:02] BAR',
    ])->map(function ($line) {
        return 'echo "' . $line . '"';
    })->implode(' && ');

    expect($remote->exec($command))->toBe([0, [
        '[00:01] FOO',
        '[00:02] BAR',
    ]]);

    $command = collect([
        '',
        '[00:01] FOO',
        '[00:02] BAR',
    ])->map(function ($line) {
        return 'echo "' . $line . '"';
    })->implode(' && ');

    expect($remote->exec($command))->toBe([0, [
        '',
        '[00:01] FOO',
        '[00:02] BAR',
    ]]);
});

test('exec not removes sanitizable output if is empty', function () {
    $remote = new LocalRepository(null);

    $command = collect([
        '[00:01] FOO',
        '[00:02] BAR',
    ])->map(function ($line) {
        return 'echo "' . $line . '"';
    })->implode(' && ');

    expect($remote->exec($command))->toBe([0, [
        '[00:01] FOO',
        '[00:02] BAR',
    ]]);
});

class LocalRepository extends RemoteRepository
{
    protected $sanitizableOutput;

    public function __construct($sanitizableOutput)
    {
        $this->sanitizableOutput = $sanitizableOutput;
    }

    public function ssh($command = null)
    {
        return $command;
    }

    public function ensureSshIsConfigured()
    {
        // ..
    }
}
