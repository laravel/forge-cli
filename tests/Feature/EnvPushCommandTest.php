<?php

use Illuminate\Support\Facades\File;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

it('can push environment variables from the generated file', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 2)->once()->andReturn(
        new Site(['id' => 2, 'name' => 'something.com']),
    );

    $file = getcwd().'/.env.forge.2';

    File::shouldReceive('exists')->once()->with($file)->andReturn(true);

    $content = "BAR=FOO\nFOO=BAR\n";

    File::shouldReceive('get')->once()->with($file)->andReturn($content);

    $this->client->shouldReceive('updateSiteEnvironment')->once()->with('personal', 1, 2, $content);

    $this->artisan('env:push')
        ->expectsSearch(
            'Which site would you like to upload the environment file to',
            answer: 2,
            search: 'something',
            answers: [2 => 'something.com'],
        )
        ->expectsConfirmation(
            'Would you like to update the site environment file with the contents of .env.forge.2?',
            'yes',
        )
        ->expectsPromptsInfo('Environment variables uploaded successfully to something.com')
        ->expectsPromptsInfo('You may need to deploy the site for the new variables to take effect.')
        ->expectsConfirmation(
            'Would you like to delete the environment file .env.forge.2 from your machine?',
            'no',
        );
});

it('can push environment variables from specific env file', function () {
    $this->client->shouldReceive('server')->with('personal', 1)->andReturn(
        new Server(['id' => 1, 'name' => 'production']),
    );

    $this->client->shouldReceive('serverSites')->with('personal', 1)->once()->andReturn(fakePaginator([
        new Site(['id' => 1, 'name' => 'pestphp.com']),
        new Site(['id' => 2, 'name' => 'something.com']),
    ]));

    $this->client->shouldReceive('organizationSite')->with('personal', 1)->once()->andReturn(
        new Site(['id' => 1, 'name' => 'pestphp.com']),
    );

    File::shouldReceive('exists')->once()->with('.env')->andReturn(true);

    $content = "BAR=FOO\nFOO=BAR\n";

    File::shouldReceive('get')->once()->with('.env')->andReturn($content);

    $this->client->shouldReceive('updateSiteEnvironment')->once()->with('personal', 1, 1, $content);

    $this->artisan('env:push', ['site' => 'pestphp.com', 'file' => '.env'])
        ->expectsPromptsInfo('Environment variables uploaded successfully to pestphp.com')
        ->expectsPromptsInfo('You may need to deploy the site for the new variables to take effect.');
});
