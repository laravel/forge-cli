<?php

use App\Repositories\RemoteRepository;
use Laravel\Forge\Resources\Server;

test('ensures current server', function () {
    (new RemoteRepository('foo'))->exec('bar');
})->throws('Current server unresolvable.');

test('exec removes sanitizable output', function () {
    $remote = new LocalSanitizableRepository(
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
    $remote = new LocalSanitizableRepository(null);

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

test('ssh username can be changed', function () {
    $remote = new LocalSshRepository();
    $remote->setSshLogin('foobar');

    expect($remote->publicSsh())->toContain(' foobar@');
});

test('ssh username is forge by default', function () {
    $remote = new LocalSshRepository();

    expect($remote->publicSsh())->toContain(' forge@');
});

class LocalSanitizableRepository extends RemoteRepository
{
    protected $sanitizableOutput;

    public function __construct($sanitizableOutput)
    {
        $this->sanitizableOutput = $sanitizableOutput;
    }

    protected function ssh($command = null)
    {
        return $command;
    }

    public function ensureSshIsConfigured()
    {
        // ..
    }
}

class LocalSshRepository extends RemoteRepository
{

    public function __construct()
    {
        $this->server = new Server(['ipAddress' => '10.0.0.1']);
    }

    public function ensureSshIsConfigured()
    {
        // ..
    }

    public function publicSsh()
    {
        return $this->ssh();
    }
}
