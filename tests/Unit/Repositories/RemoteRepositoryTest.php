<?php

use App\Repositories\RemoteRepository;
use Laravel\Forge\Resources\Server;

test('ensures current server', function () {
    (new RemoteRepository('foo'))->exec('bar');
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
        return 'echo "'.$line.'"';
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
        return 'echo "'.$line.'"';
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
        return 'echo "'.$line.'"';
    })->implode(' && ');

    expect($remote->exec($command))->toBe([0, [
        '[00:01] FOO',
        '[00:02] BAR',
    ]]);
});

test('ssh shares connections when supported', function () {
    $ssh = (new SshCommandRepository('/sockets', true))->sshCommand('ls');

    expect($ssh)->toContain('-o ControlMaster=auto')
        ->toContain('-o ControlPersist=100')
        ->toContain('-o ControlPath=/sockets/%h-%p-%r')
        ->toEndWith('-p 22 -t forge@127.0.0.1 ls');
});

test('ssh does not share connections when unsupported', function () {
    $ssh = (new SshCommandRepository('/sockets', false))->sshCommand('ls');

    expect($ssh)->not->toContain('Control')
        ->toContain('-o ConnectTimeout=5')
        ->toEndWith('-p 22 -t forge@127.0.0.1 ls');
});

class LocalRepository extends RemoteRepository
{
    protected $sanitizableOutput;

    public function __construct($sanitizableOutput)
    {
        $this->sanitizableOutput = $sanitizableOutput;
    }

    protected function ssh($command = null, $user = null)
    {
        return $command;
    }

    public function ensureSshIsConfigured()
    {
        // ..
    }
}

class SshCommandRepository extends RemoteRepository
{
    public function __construct($socketsPath, protected bool $connectionSharing)
    {
        parent::__construct($socketsPath);

        $this->server = new Server(['ipAddress' => '127.0.0.1']);
    }

    public function sshCommand($command)
    {
        return $this->ssh($command);
    }

    protected function supportsConnectionSharing()
    {
        return $this->connectionSharing;
    }
}
