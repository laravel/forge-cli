#!/usr/bin/env php
<?php

use phpseclib3\Crypt\Common\Formats\Keys\OpenSSH;
use phpseclib3\Crypt\EC;

require file_exists(__DIR__.'/../vendor/autoload.php') ? __DIR__.'/../vendor/autoload.php' : __DIR__.'/../../../autoload.php';

OpenSSH::setComment('forge-cli-generated-key');
$private = EC::createKey('Ed25519');

$public = $private->getPublicKey()->toString('OpenSSH');
$private = $private->toString('OpenSSH');

echo json_encode([
    'private' => $private,
    'public' => $public,
]);
