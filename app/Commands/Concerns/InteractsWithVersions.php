<?php

namespace App\Commands\Concerns;

trait InteractsWithVersions
{
    /**
     * The latest version resolver.
     *
     * @var callable|null
     */
    protected static $latestVersionResolver = null;

    /**
     * Warns the user about the latest version of Forge CLI.
     *
     * @return void
     */
    protected function ensureLatestVersion()
    {
        $current = 'v'.config('app.version');

        if (version_compare($remote = $this->getLatestVersion(), $current) > 0) {
            $this->warnStep(['You are using an outdated version %s of Forge CLI. Please update to %s.', $current, $remote]);
        }
    }

    /**
     * Returns the latest version.
     *
     * @return string
     */
    protected function getLatestVersion()
    {
        $resolver = static::$latestVersionResolver ?? function () {
            try {
                $context = stream_context_create([
                    'http' => ['timeout' => 5],
                ]);

                $response = file_get_contents(
                    'https://packagist.org/p2/laravel/forge-cli.json',
                    false,
                    $context
                );

                if ($response === false) {
                    return 'v'.config('app.version');
                }

                $package = json_decode($response, true);

                return collect($package['packages']['laravel/forge-cli'])
                    ->first()['version'];
            } catch (\Throwable $e) {
                return 'v'.config('app.version');
            }
        };

        if (is_null($this->config->get('latest_version_verified_at'))) {
            $this->config->set('latest_version_verified_at', now()->timestamp);
        }

        if (is_null($this->config->get('latest_version'))) {
            $this->config->set('latest_version', call_user_func($resolver));
        }

        if ($this->config->get('latest_version_verified_at') < now()->subDays(1)->timestamp) {
            $this->config->set('latest_version', call_user_func($resolver));
            $this->config->set('latest_version_verified_at', now()->timestamp);
        }

        return $this->config->get('latest_version');
    }

    /**
     * Sets the latest version resolver.
     *
     * @param  callable  $resolver
     * @return void
     */
    public static function resolveLatestVersionUsing($resolver)
    {
        static::$latestVersionResolver = $resolver;
    }
}
