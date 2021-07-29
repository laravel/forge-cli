#!/usr/bin/env php
<?php

use phpseclib3\Crypt\RSA;

require file_exists(__DIR__.'/../vendor/autoload.php') ? __DIR__.'/../vendor/autoload.php' : __DIR__.'/../../../autoload.php';

/** @var \phpseclib3\Crypt\RSA\PrivateKey $private */
$private = RSA::createKey();
/** @var \phpseclib3\Crypt\RSA\PrivateKey $public */
$public = $private->getPublicKey();

echo json_encode([
    'private' => (string) $private,
    'public' => (string) $public,
]);
