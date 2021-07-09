<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use LaravelZero\Framework\Commands\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    /**
     * The application configuration.
     *
     * @var \App\Repositories\ConfigRepository
     */
    protected $config;

    /**
     * Creates a new command instance.
     *
     * @param  \App\Repositories\ConfigRepository  $config
     * @return void
     */
    public function __construct(ConfigRepository $config)
    {
        parent::__construct();

        $this->config = $config;
    }
}
