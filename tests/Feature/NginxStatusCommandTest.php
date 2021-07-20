<?php

it('can display the nginx status running', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222'],
    );

    $this->remote->shouldReceive('exec')->andReturn([0]);

    $this->artisan('nginx:status')->expectsOutput('Nginx service is [running].');
});

it('can display the nginx status as inactive', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production', 'ipAddress' => '123.456.789.222'],
    );

    $this->remote->shouldReceive('exec')->andReturn([3]);

    $this->artisan('nginx:status')->expectsOutput('Nginx service is [inactive].');
});
