<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use App\Repositories\ForgeRepository;
use Laravel\Forge\Forge;
use LaravelZero\Framework\Commands\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    /**
     * The configuration repository.
     *
     * @var \App\Repositories\ConfigRepository
     */
    protected $config;

    /**
     * The forge repository.
     *
     * @var \App\Repositories\ForgeRepository
     */
    protected $forge;

    /**
     * Creates a new command instance.
     *
     * @param  \App\Repositories\ConfigRepository  $config
     * @param  \App\Repositories\ForgeRepository  $forge
     * @return void
     */
    public function __construct(ConfigRepository $config, ForgeRepository $forge)
    {
        parent::__construct();

        $this->config = $config;
        $this->forge = $forge;
    }
}
