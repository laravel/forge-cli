<?php

it('ensures api token', function () {
    $this->config->flush();

    $this->forge->servers();
})->throws('Please authenticate using the \'login\' command before proceeding.');
