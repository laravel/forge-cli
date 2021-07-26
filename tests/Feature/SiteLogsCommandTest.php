<?php

it('can retrieve logs from sites with an menu', function () {
    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('siteLogs')
        ->andReturn((object) [
            'content' => "   [00:01] FOO\n[00:02] BAR\n   ",
        ]);

    $this->artisan('site:logs')
        ->expectsChoice('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 'pestphp.com', [
            'pestphp.com', 'something.com',
        ])->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});

it('can retrieve logs from sites with an option', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('sites')->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('siteLogs')
        ->andReturn((object) [
            'content' => "   [00:01] FOO\n[00:02] BAR\n   ",
        ]);

    $this->artisan('site:logs', ['site' => 'pestphp.com'])
        ->expectsOutput('  ▕ [00:01] FOO')
        ->expectsOutput('  ▕ [00:02] BAR');
});
