<?php

it('can create ssh keys', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'id' => 1,
            'name' => 'production',
        ]);

    $this->keys->shouldReceive('keysPath')
        ->andReturn('/home/driesvints/.ssh');

    $this->keys->shouldReceive('local')
        ->andReturn([
            '/home/driesvints/.ssh/id_rsa.pub',
        ]);

    $this->keys->shouldReceive('create')->with('driesvints')->once()->andReturn([
        'driesvints_rsa.pub',
        'MY KEY Content',
    ]);

    $this->forge->shouldReceive('createSSHKey')->with(1, [
        'name' => 'driesvints',
        'key' => 'MY KEY Content',
    ], true)->once();

    $this->artisan('ssh:configure')
        ->expectsChoice('<fg=yellow>‣</> <options=bold>Which Key Would You Like To Use</>', 0, [
            '<comment>Create new key</comment>',
            '<comment>Reuse</comment> id_rsa.pub',
        ])->expectsQuestion('<fg=yellow>‣</> <options=bold>What Should The SSH Key Be Named</>', 'driesvints')
        ->expectsOutput('==> Creating Key [driesvints_rsa.pub]')
        ->expectsOutput('==> Adding Key [driesvints_rsa.pub] With The Name [driesvints] To Server [production]')
        ->expectsOutput('==> SSH Key Based Secure Authentication Configured Successfully');
});

it('can reuse ssh keys', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'id' => 1,
            'name' => 'production',
        ]);

    $this->keys->shouldReceive('keysPath')
        ->andReturn('/home/driesvints/.ssh');

    $this->keys->shouldReceive('local')
        ->andReturn([
            '/home/driesvints/.ssh/id_rsa.pub',
        ]);

    $this->keys->shouldReceive('get')->with('/home/driesvints/.ssh/id_rsa.pub')->once()->andReturn([
        'id_rsa.pub',
        'MY KEY Content',
    ]);

    $this->forge->shouldReceive('createSSHKey')->with(1, [
        'name' => 'driesvints',
        'key' => 'MY KEY Content',
    ], true)->once();

    $this->artisan('ssh:configure')
        ->expectsChoice('<fg=yellow>‣</> <options=bold>Which Key Would You Like To Use</>', '<comment>Reuse</comment> id_rsa.pub', [
            '<comment>Create new key</comment>',
            '<comment>Reuse</comment> id_rsa.pub',
        ])->expectsQuestion('<fg=yellow>‣</> <options=bold>What Should The SSH Key Be Named In Forge</>', 'driesvints')
        ->expectsOutput('==> Adding Key [id_rsa.pub] With The Name [driesvints] To Server [production]')
        ->expectsOutput('==> SSH Key Based Secure Authentication Configured Successfully');
});
