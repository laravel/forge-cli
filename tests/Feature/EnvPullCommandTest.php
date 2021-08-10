<?php

use Illuminate\Support\Facades\File;

it('can pull environment variables generated file', function () {
    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('site')->andReturn(
        (object) ['id' => 2, 'name' => 'something.com'],
    );

    $file = getcwd().'/.env.forge.2';

    File::shouldReceive('exists')->once()->with($file)->andReturn(false);
    File::shouldReceive('delete')->once()->with($file)->andReturn(false);

    $content = "BAR=FOO\nFOO=BAR\n";

    $this->forge->shouldReceive('siteEnvironmentFile')->once()->with(1, 2)->andReturn($content);

    File::shouldReceive('put')->once()->with($file, $content);

    $this->artisan('env:pull')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Download The Environment File From</>', 2)
        ->expectsOutput('==> Environment Variables Written To [.env.forge.2]');
});

it('can pull environment variables specific env file', function () {
    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('site')->andReturn(
        (object) ['id' => 1, 'name' => 'pestphp.com'],
    );

    File::shouldReceive('delete')->once()->with('.env')->andReturn(false);

    $content = "FOO=BAR\nBAR=FOO\n";

    $this->forge->shouldReceive('siteEnvironmentFile')->once()->with(1, 1)->andReturn($content);

    File::shouldReceive('put')->once()->with('.env', $content);

    $this->artisan('env:pull', ['site' => 'pestphp.com', 'file' => '.env'])
        ->expectsOutput('==> Environment Variables Written To [.env]');
});
