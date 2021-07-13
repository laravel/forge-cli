<?php

it('allows to create ssh connections', function () {
    $this->config->set('server', 1);

    $this->forge->shouldReceive('server')
        ->once()
        ->with(1)
        ->andReturn((object) [
            'ipAddress' => '123.456.789.000',
        ]);

    $this->shell->shouldReceive('passthru')->with(
        'ssh -t forge@123.456.789.000',
    )->once();

    $this->artisan('ssh')->assertExitCode(0);
});
