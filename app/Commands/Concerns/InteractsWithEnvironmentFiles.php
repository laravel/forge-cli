<?php

namespace App\Commands\Concerns;

use Laravel\Forge\Resources\Site;

trait InteractsWithEnvironmentFiles
{
    /**
     * Gets the "local" environment file name.
     *
     * @param  Site  $site
     * @return string
     */
    protected function getEnvironmentFile($site)
    {
        return $this->argument('file') ?: (getcwd().'/.env.forge.'.$site->id);
    }
}
