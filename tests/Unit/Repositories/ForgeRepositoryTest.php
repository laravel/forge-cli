<?php

use Laravel\Forge\Resources\Server;

it('ensures usage of api token from environment', function () {
    $this->config->flush();

    $this->client->shouldReceive('servers')->andReturn([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000', 'tags' => [['name' => 'first'], ['name' => 'second']]]),
    ]);

    $this->forge->servers();
});

it('ensures current server', function () {
    $this->config->flush();

    $this->client->shouldReceive('servers')->andReturn([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000', 'tags' => [['name' => 'first'], ['name' => 'second']]]),
    ]);

    $this->client->shouldReceive('sites')->with(1)->andReturn([
        new Server(['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.000', 'tags' => [['name' => 'first'], ['name' => 'second']]]),
    ]);

    $this->forge->sites(1);
});
