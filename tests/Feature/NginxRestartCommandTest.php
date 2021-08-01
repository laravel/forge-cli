<?php

it('can restart nginx', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('rebootNginx');

    $this->artisan('nginx:restart')
        ->expectsConfirmation(
            'The sites may become unavailable while the <comment>[Nginx]</comment> service restarts. Continue?',
            'yes',
        )->expectsOutput('==> Nginx Restart Initiated Successfully.');
});
