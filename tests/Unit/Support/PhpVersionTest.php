<?php

use App\Support\PhpVersion;

test('release', function () {
    expect(PhpVersion::of('php74')->release())->toBe('7.4');
    expect(PhpVersion::of('php80')->release())->toBe('8.0');
    expect(PhpVersion::of('php81')->release())->toBe('8.1');
    expect(PhpVersion::of('php82')->release())->toBe('8.2');
    expect(PhpVersion::of('PHP 8.4')->release())->toBe('8.4');
});

test('binary', function () {
    expect(PhpVersion::of('php74')->binary())->toBe('php7.4');
    expect(PhpVersion::of('php80')->binary())->toBe('php8.0');
    expect(PhpVersion::of('php81')->binary())->toBe('php8.1');
    expect(PhpVersion::of('php82')->binary())->toBe('php8.2');
    expect(PhpVersion::of('PHP 8.4')->binary())->toBe('php8.4');
});

test('forge key', function () {
    expect(PhpVersion::of('php74')->forgeKey())->toBe('php74');
    expect(PhpVersion::of('PHP 8.4')->forgeKey())->toBe('php84');
});

test('service name', function () {
    expect(PhpVersion::of('php74')->serviceName())->toBe('php7.4-fpm');
    expect(PhpVersion::of('PHP 8.4')->serviceName())->toBe('php8.4-fpm');
});
