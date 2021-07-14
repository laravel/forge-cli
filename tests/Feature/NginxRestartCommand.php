<?php

it('can restart nginx', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('rebootNginx');

    $this->artisan('nginx:restart')
        ->expectsConfirmation(
            'While the <comment>[Nginx]</comment> service restarts, sites may become unavailable. Wish to proceed?',
            'yes',
        )->expectsOutput('Nginx restart initiated successfully.');
});
