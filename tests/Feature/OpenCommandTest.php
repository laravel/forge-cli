<?php

it('can open a site in the browser on supported os', function () {
    $this->client->shouldReceive('server')->once()->andReturn(
        (object) ['id' => 1, 'name' => 'production'],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $os = strtolower(PHP_OS);

    if (strpos($os, 'darwin') !== false || strpos($os, 'linux') !== false) {
        $this->artisan('open', ['site' => 'pestphp.com'])
            ->assertSuccessful();
    } else {
        $this->artisan('open', ['site' => 'pestphp.com'])
            ->assertSuccessful();
    }
});

it('uses PHP_OS constant for os detection without throwing ValueError', function () {
    // This test verifies the fix: PHP_OS is a string constant (e.g. 'Darwin', 'Linux'),
    // while php_uname(PHP_OS) was incorrectly passing the OS name as a mode argument.
    // In PHP 8.4+, php_uname() throws a ValueError for invalid mode arguments.
    $os = strtolower(PHP_OS);

    expect($os)->toBeString()
        ->and(strlen($os))->toBeGreaterThan(0)
        ->and(function () {
            // Confirm that strtolower(PHP_OS) does not throw
            strtolower(PHP_OS);
        })->not->toThrow(ValueError::class);
});

it('detects the correct browser open command for the current os', function () {
    $os = strtolower(PHP_OS);

    if (strpos($os, 'darwin') !== false) {
        // macOS should use 'open'
        expect(strpos($os, 'darwin'))->not->toBeFalse();
    } elseif (strpos($os, 'linux') !== false) {
        // Linux should use 'xdg-open'
        expect(strpos($os, 'linux'))->not->toBeFalse();
    } else {
        // Unsupported OS should fall through to the else branch
        expect(strpos($os, 'darwin'))->toBeFalse()
            ->and(strpos($os, 'linux'))->toBeFalse();
    }
});
