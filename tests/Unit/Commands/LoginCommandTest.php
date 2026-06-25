<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->config->flush();
    putenv('FORGE_API_TOKEN');

    $this->client->shouldReceive('user')->andReturn((object) [
        'email' => 'nuno@laravel.com',
    ]);

    $this->client->shouldReceive('servers')->andReturn([
        (object) ['id' => 1],
    ]);
});

afterEach(function () {
    putenv('FORGE_API_TOKEN');
});

it('reads the token from --token-file', function () {
    $path = tempnam(sys_get_temp_dir(), 'forge-token-');
    file_put_contents($path, "  filetoken-abc\n");

    try {
        $this->artisan("login --token-file={$path}")
            ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');

        expect($this->config->get('token'))->toBe('filetoken-abc');
    } finally {
        @unlink($path);
    }
});

it('errors when --token-file points at a missing path', function () {
    $missing = sys_get_temp_dir().'/forge-token-does-not-exist-'.uniqid();

    $this->artisan("login --token-file={$missing}");
})->throws('Unable to read token file');

it('falls back to FORGE_API_TOKEN when no flag or file is provided', function () {
    putenv('FORGE_API_TOKEN=envtoken-xyz');

    $this->artisan('login')
        ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');

    expect($this->config->get('token'))->toBe('envtoken-xyz');
});

it('prefers --token over FORGE_API_TOKEN', function () {
    putenv('FORGE_API_TOKEN=envtoken-xyz');

    $this->artisan('login --token=flagtoken')
        ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');

    expect($this->config->get('token'))->toBe('flagtoken');
});

it('prefers --token-file over FORGE_API_TOKEN', function () {
    putenv('FORGE_API_TOKEN=envtoken-xyz');

    $path = tempnam(sys_get_temp_dir(), 'forge-token-');
    file_put_contents($path, 'filetoken-abc');

    try {
        $this->artisan("login --token-file={$path}")
            ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');

        expect($this->config->get('token'))->toBe('filetoken-abc');
    } finally {
        @unlink($path);
    }
});

it('prefers --token over --token-file', function () {
    $path = tempnam(sys_get_temp_dir(), 'forge-token-');
    file_put_contents($path, 'filetoken-abc');

    try {
        $this->artisan("login --token=flagtoken --token-file={$path}")
            ->expectsOutput('==> Authenticated Successfully As [nuno@laravel.com]');

        expect($this->config->get('token'))->toBe('flagtoken');
    } finally {
        @unlink($path);
    }
});

it('warns when an interactively pasted token may have been truncated', function () {
    $longToken = str_repeat('a', 1024);

    $this->artisan('login')
        ->expectsQuestion(
            '<fg=yellow>‣</> <options=bold>Please Enter Your Forge API Token</>',
            $longToken
        )
        ->expectsOutputToContain('May Have Been Truncated');

    expect($this->config->get('token'))->toBe($longToken);
});

it('does not warn about truncation when a long token comes from --token-file', function () {
    $longToken = str_repeat('a', 2048);

    $path = tempnam(sys_get_temp_dir(), 'forge-token-');
    file_put_contents($path, $longToken);

    try {
        $this->artisan("login --token-file={$path}")
            ->doesntExpectOutputToContain('May Have Been Truncated');

        expect($this->config->get('token'))->toBe($longToken);
    } finally {
        @unlink($path);
    }
});

it('rejects an empty token', function () {
    putenv('FORGE_API_TOKEN');

    $this->artisan('login')
        ->expectsQuestion(
            '<fg=yellow>‣</> <options=bold>Please Enter Your Forge API Token</>',
            '   '
        );
})->throws('A Forge API token is required.');
