<?php

use App\Repositories\ConfigRepository;

it('sets and gets values', function () {
    $config = resolve(ConfigRepository::class)->flush();

    $config->set('string', 'bar')
        ->set('int', 10)
        ->set('array', ['bar', 10]);

    expect($config)
        ->get('string')->toBe('bar')
        ->get('int')->toBe(10)
        ->get('array')->toBe(['bar', 10])
        ->all()->toBe([
            'string' => 'bar',
            'int' => 10,
            'array' => ['bar', 10],
        ]);
});
