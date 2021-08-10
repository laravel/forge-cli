<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;

class EnvPullCommand extends Command
{
    use Concerns\InteractsWithEnvironmentFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'env:pull {site? : The site name} {file? : File to write the environment variables to}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Download the environment file for the given site';

    /**
     * Execute the console command.
     *
     * @return int|void
     */
    public function handle()
    {
        $siteId = $this->askForSite('Which site would you like to download the environment file from');

        $server = $this->currentServer();
        $file = $this->getEnvironmentFile(
            $site = $this->forge->site($server->id, $siteId)
        );

        if (is_null($this->argument('file')) && File::exists($file) && ! $this->confirmStep(
            ['File already exists with the name: %s. Would you like to overwrite it?', basename($file)]
        )) {
            return 0;
        }

        File::delete($file);

        File::put(
            $file,
            $this->forge->siteEnvironmentFile($this->currentServer()->id, $site->id),
        );

        $this->successfulStep(['Environment variables written to %s', basename($file)]);
    }
}
