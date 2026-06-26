<?php

use Laravel\Forge\Resources\Server;

it('ensures usage of api token from environment', function () {
    $this->config->flush();

    $_SERVER['FORGE_API_TOKEN'] = 'foo';

    $this->client->shouldReceive('servers')->with('personal')->andReturn(fakePaginator([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000']),
    ]));

    $this->forge->servers('personal');
});

it('ensures current server', function () {
    $this->config->flush();
    $this->config->set('token', '123123213');

    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000']),
    );

    $this->forge->server('personal', 1);
});
