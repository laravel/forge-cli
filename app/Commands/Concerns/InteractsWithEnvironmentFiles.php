<?php

namespace App\Commands\Concerns;

trait InteractsWithEnvironmentFiles
{
    /**
     * Gets the "local" environment file name.
     *
     * @param  \Laravel\Forge\Resources\Site  $site
     * @return string
     */
    protected function getEnvironmentFile($site)
    {
        return $this->argument('file') ?: (getcwd().'/.env.forge.'.$site->id);
    }
}
