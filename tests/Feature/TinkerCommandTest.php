<?php

it('can tinker with sites', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'databaseType' => 'mysql'],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->with(1, 2)->once()->andReturn(
        (object) ['id' => 2, 'name' => 'something.com', 'phpVersion' => 'php71', 'username' => 'user-in-isolation'],
    );

    $this->remote
        ->shouldReceive('passthru')
        ->with('cd /home/user-in-isolation/something.com && php7.1 artisan tinker')
        ->andReturn(0);

    $this->artisan('tinker', ['site' => 2])
        ->assertExitCode(0)
        ->expectsOutput('==> Establishing Tinker Connection');
});
