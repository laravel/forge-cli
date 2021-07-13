<?php

namespace App\Repositories;

use App\Exceptions;
use Exception;
use Laravel\Forge\Exceptions\NotFoundException;

/**
 * @mixin \Laravel\Forge\Forge
 */
class ForgeRepository
{
    /**
     * The configuration repository.
     *
     * @var \App\Repositories\ConfigRepository
     */
    protected $config;

    /**
     * The client.
     *
     * @var \Laravel\Forge\Forge
     */
    protected $client;

    /**
     * Creates a new repository instance.
     *
     * @param  \App\Repositories\ConfigRepository  $config
     * @param  \Laravel\Forge\Forge  $client
     * @return void
     */
    public function __construct($config, $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Sets the client.
     *
     * @param  \Laravel\Forge\Forge  $client
     * @return void
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Pass other method calls down to the underlying client.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->ensureApiToken();

        try {
            return $this->client->{$method}(...$parameters);
        } catch (Exception $e) {
            if ($e instanceof NotFoundException) {
                throw new Exceptions\NotFoundException(
                    $e->getMessage(), 404, $e
                );
            }

            if ($e instanceof Exception && $e->getMessage() == 'Unauthorized') {
                throw new Exceptions\UnauthorizedException(
                    'Your API Token is invalid.', 403, $e
                );
            }

            throw $e;
        }
    }

    /**
     * Ensure an api token is defined on the client.
     *
     * @return void
     */
    protected function ensureApiToken()
    {
        $token = $this->config->get('token');

        if ($token == null) {
            throw new Exceptions\UnauthorizedException(
                'Please authenticate using the \'login\' command before proceeding.'
            );
        }

        $this->client->setApiKey($token);
    }
}
