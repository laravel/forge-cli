<?php

namespace App\Repositories;

use App\Clients\Forge;
use Exception;
use GuzzleHttp;
use Illuminate\Support\Collection;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Resources\Organization;

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
     * The token that overrides the configured token, if any.
     *
     * @var string|null
     */
    protected $token;

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
     * Get the api token for the given organization, or for the current one.
     *
     * @param  string|null  $organization
     * @return string|null
     */
    public function token($organization = null)
    {
        $organization ??= $this->config->get('organization');

        return $this->config->get('tokens', [])[$organization]
            ?? $this->config->get('token', $_SERVER['FORGE_API_TOKEN'] ?? getenv('FORGE_API_TOKEN') ?: null);
    }

    /**
     * Get all of the distinct api tokens.
     *
     * @return array<int, string>
     */
    public function tokens()
    {
        return collect($this->config->get('tokens', []))
            ->values()
            ->push($this->config->get('token', $_SERVER['FORGE_API_TOKEN'] ?? getenv('FORGE_API_TOKEN') ?: null))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Execute the given callback using the given api token.
     *
     * @template TReturn
     *
     * @param  string  $token
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public function usingToken($token, $callback)
    {
        $previous = $this->token;

        $this->token = $token;

        try {
            return $callback();
        } finally {
            $this->token = $previous;
        }
    }

    /**
     * Get the organizations of all api tokens, keyed by slug.
     *
     * @return Collection<string, array{0: Organization, 1: string}>
     */
    public function organizationsWithTokens()
    {
        return collect($this->tokens())
            ->flatMap(fn ($token) => $this->usingToken(
                $token,
                fn () => collect($this->organizations())->map(fn ($organization) => [$organization, $token]),
            ))
            ->unique(fn ($item) => $item[0]->slug)
            ->keyBy(fn ($item) => $item[0]->slug);
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
        $token = $this->token ?? $this->token();

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
