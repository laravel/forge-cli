<?php

use App\Support\PhpVersion;

test('release', function () {
    expect(PhpVersion::of('php74')->release())->toBe('7.4');
    expect(PhpVersion::of('php80')->release())->toBe('8.0');
    expect(PhpVersion::of('php81')->release())->toBe('8.1');
    expect(PhpVersion::of('php82')->release())->toBe('8.2');
});

test('binary', function () {
    expect(PhpVersion::of('php74')->binary())->toBe('php7.4');
    expect(PhpVersion::of('php80')->binary())->toBe('php8.0');
    expect(PhpVersion::of('php81')->binary())->toBe('php8.1');
    expect(PhpVersion::of('php82')->binary())->toBe('php8.2');
});
