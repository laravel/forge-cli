<?php

use Laravel\Forge\Resources\Server;

it('can create ssh keys', function () {
    $this->config->set('server', 1);

    $this->client->shouldReceive('servers')->with('personal')->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'staging', 'revoked' => false]),
    ]));

    $this->client->shouldReceive('server')
        ->once()
        ->with('personal', 1)
        ->andReturn(new Server([
            'id' => 1,
            'name' => 'production',
        ]));

    $this->keys->shouldReceive('keysPath')
        ->andReturn('/home/driesvints/.ssh');

    $this->keys->shouldReceive('local')
        ->andReturn([
            '/home/driesvints/.ssh/id_rsa.pub',
        ]);

    $this->keys->shouldReceive('create')->with('driesvints')->once()->andReturn([
        'driesvints_rsa.pub',
        "MY KEY Content\n",
    ]);

    $this->client->shouldReceive('createSshKey')->with('personal', 1, [
        'name' => 'driesvints',
        'key' => 'MY KEY Content',
        'username' => 'morales2k',
    ])->once();

    $this->remote->shouldReceive('resolvePrivateKeyUsing')->once();
    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->artisan('ssh:configure')
        ->expectsSearch(
            'Which server would you like to configure the SSH key based secure authentication',
            answer: 1,
            search: 'production',
            answers: [1 => 'production'],
        )
        ->expectsQuestion('Which key would you like to use', 'create')
        ->expectsQuestion('What should the SSH key be named', 'driesvints')
        ->expectsQuestion('What username should we use for the selected server', 'morales2k')
        ->expectsPromptsInfo('Creating key driesvints_rsa.pub')
        ->expectsPromptsInfo('Adding key driesvints_rsa.pub with the name driesvints to server production')
        ->expectsPromptsInfo('SSH key based secure authentication configured successfully');
});

it('can reuse ssh keys', function () {
    $this->config->set('server', 2);

    $this->client->shouldReceive('servers')->with('personal')->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'revoked' => false]),
        new Server(['id' => 2, 'name' => 'staging', 'revoked' => false]),
    ]));

    $this->client->shouldReceive('server')
        ->once()
        ->with('personal', 2)
        ->andReturn(new Server([
            'id' => 2,
            'name' => 'staging',
        ]));

    $this->keys->shouldReceive('keysPath')
        ->andReturn('/home/driesvints/.ssh');

    $this->keys->shouldReceive('local')
        ->andReturn([
            '/home/driesvints/.ssh/id_rsa.pub',
        ]);

    $this->keys->shouldReceive('get')->with('/home/driesvints/.ssh/id_rsa.pub')->once()->andReturn([
        'id_rsa.pub',
        "\nMY KEY Content\n",
    ]);

    $this->client->shouldReceive('createSshKey')->with('personal', 2, [
        'name' => 'driesvints',
        'key' => 'MY KEY Content',
        'username' => 'morales2k',
    ])->once();

    $this->remote->shouldReceive('resolvePrivateKeyUsing')->once();
    $this->remote->shouldReceive('ensureSshIsConfigured')->once();

    $this->artisan('ssh:configure', ['server' => 2])
        ->expectsQuestion('Which key would you like to use', '/home/driesvints/.ssh/id_rsa.pub')
        ->expectsQuestion('What should the SSH key be named in Forge', 'driesvints')
        ->expectsQuestion('What username should we use for the selected server', 'morales2k')
        ->expectsPromptsInfo('Adding key id_rsa.pub with the name driesvints to server staging')
        ->expectsPromptsInfo('SSH key based secure authentication configured successfully');
});
