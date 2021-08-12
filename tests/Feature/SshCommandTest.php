<?php

it('can create ssh connections', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->remote->shouldReceive('passthru')->andReturn(0);

    $this->artisan('ssh')->assertExitCode(0);
});

it('can create ssh connections for a specific server', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('servers')
        ->once()
        ->andReturn([
            (object) [
                'id' => 1,
                'name' => 'production',
                'ipAddress' => '123.456.789.000',
            ],
        ]);

    $this->forge->shouldReceive('server')
        ->twice()
        ->with(1)
        ->andReturn((object) [
            'id' => 1,
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->remote->shouldReceive('passthru')->andReturn(0);

    $this->artisan('ssh production')->assertExitCode(0);
});

it('can create ssh connections for a site', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('servers')
        ->once()
        ->andReturn([
            (object) [
                'id' => 1,
                'name' => 'production',
                'ipAddress' => '123.456.789.000',
            ],
        ]);

    $this->forge->shouldReceive('server')
        ->twice()
        ->with(1)
        ->andReturn((object) [
            'id' => 1,
            'name' => 'production',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->forge->shouldReceive('sites')
        ->once()
        ->with(1)
        ->andReturn([
            (object) [
                'id' => 1,
                'name' => 'staging.test',
                'username' => 'staging',
            ],
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');
    $this->remote->shouldReceive('setSshUser')->with('staging');

    $this->remote->shouldReceive('passthru')->andReturn(0);

    $this->artisan('ssh production staging.test')->assertExitCode(0);
});

it('can not create ssh connections when ssh key is missing', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'name' => 'staging',
            'ipAddress' => '123.456.789.000',
        ]);

    $this->remote->shouldReceive('ensureSshIsConfigured');

    $this->remote->shouldReceive('passthru')->andReturn(255);

    $this->artisan('ssh');
})->throws('Unable to connect to remove server. Have you configured an SSH key?');
