<?php

it('can create ssh connections', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'ipAddress' => '123.456.789.000',
        ]);

    $this->shell->shouldReceive('passthru')->with(
        'ssh -t forge@123.456.789.000',
    )->andReturn(0);

    $this->artisan('ssh')->assertExitCode(0);
});

it('can not create ssh connections when ssh key is missing', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'ipAddress' => '123.456.789.000',
        ]);

    $this->shell->shouldReceive('passthru')->with(
        'ssh -t forge@123.456.789.000',
    )->andReturn(255);

    $this->artisan('ssh');
})->throws('Unable to connect to remove server. Have you configured an SSH Key?');
