<?php

it('can display the nginx status running', function () {
    $this->remote->shouldReceive('exec')
        ->with('systemctl is-active --quiet nginx')
        ->andReturn([0]);

    $this->artisan('nginx:status')
        ->expectsPromptsInfo('Nginx is up & running');
});

it('can display the nginx status as inactive', function () {
    $this->remote->shouldReceive('exec')
        ->with('systemctl is-active --quiet nginx')
        ->andReturn([3]);

    $this->artisan('nginx:status');
})->throws('Service is not running.');
