<?php

use Laravel\Forge\Resources\Server;

it('can restart nginx', function () {
    $server = Mockery::mock(Server::class)->makePartial();
    $server->id = 1;
    $server->name = 'production';
    $server->shouldReceive('rebootNginx')->once();

    $this->client->shouldReceive('server')->with('personal', 1)->once()->andReturn(
        $server,
    );

    $this->artisan('nginx:restart')
        ->expectsConfirmation(
            'The sites may become unavailable while the Nginx service restarts. Continue?',
            'yes',
        )->expectsPromptsInfo('Nginx restart initiated successfully.');
});
