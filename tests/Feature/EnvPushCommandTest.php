<?php

use Illuminate\Support\Facades\File;

it('can push environment variables from the generated file', function () {
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

    File::shouldReceive('exists')->once()->with($file)->andReturn(true);

    $content = "BAR=FOO\nFOO=BAR\n";

    File::shouldReceive('get')->once()->with($file)->andReturn($content);

    $this->forge->shouldReceive('updateSiteEnvironmentFile')->once()->with(1, 2, $content);

    $this->artisan('env:push')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Upload The Environment File To</>', 2)
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Would You Like Update The Site Environment File With The Contents Of The File <comment>[.env.forge.2]</comment></>', 2)
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Would You Like To Delete The Environment File <comment>[.env.forge.2]</comment> From Your Machine</>', false)
        ->expectsOutput('==> Uploading [.env.forge.2] Environment File')
        ->expectsOutput('==> Environment Variables Uploaded Successfully To [something.com]');
});

it('can push environment variables from specific env file', function () {
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

    File::shouldReceive('exists')->once()->with('.env')->andReturn(true);

    $content = "BAR=FOO\nFOO=BAR\n";

    File::shouldReceive('get')->once()->with('.env')->andReturn($content);

    $this->forge->shouldReceive('updateSiteEnvironmentFile')->once()->with(1, 2, $content);

    $this->artisan('env:push', ['site' => 'pestphp.com', 'file' => '.env'])
        ->expectsOutput('==> Uploading [.env] Environment File')
        ->expectsOutput('==> Environment Variables Uploaded Successfully To [something.com]');
});
