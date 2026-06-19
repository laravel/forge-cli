<?php

namespace App\Repositories;

use App\Clients\Forge;
use Exception;
use GuzzleHttp;
use Laravel\Forge\Exceptions\NotFoundException;

/**
 * @mixin Forge
 */
class ForgeRepository
{
    /**
     * The configuration repository.
     *
     * @var ConfigRepository
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
     * @param  ConfigRepository  $config
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
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
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
                abort(1, $e->getMessage());
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
        $token = $this->config->get('token', $_SERVER['FORGE_API_TOKEN'] ?? getenv('FORGE_API_TOKEN') ?: null);

        abort_if($token == null, 1, 'Please authenticate using the \'login\' command before proceeding.');

        $guzzle = new GuzzleHttp\Client([
            'base_uri' => isset($_SERVER['FORGE_API_BASE']) ? $_SERVER['FORGE_API_BASE'] : 'https://forge.laravel.com/api/',
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'User-Agent' => 'Laravel Forge CLI/v'.config('app.version'),
            ],
        ]);

        $this->client->setApiKey($token, $guzzle);
    }
}
