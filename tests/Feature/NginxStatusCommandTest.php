<?php

it('can display the nginx status active', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222'],
    );

    $this->shell->shouldReceive('exec')->andReturn([0]);

    $this->artisan('nginx:status')->expectsOutput('Nginx is [active].');
});

it('can display the nginx status as inactive', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222'],
    );

    $this->shell->shouldReceive('exec')->andReturn([3]);

    $this->artisan('nginx:status')->expectsOutput('Nginx is [inactive].');
});
