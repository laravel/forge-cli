<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;

class EnvPushCommand extends Command
{
    use Concerns\InteractsWithEnvironmentFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'env:push {site? : The site name} {file? : File to upload the environment variables from}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Upload the environment file for the given site';

    /**
     * Execute the console command.
     *
     * @return int|void
     */
    public function handle()
    {
        $siteId = $this->askForSite('Which site would you like to upload the environment file to');

        $server = $this->currentServer();
        $file = $this->getEnvironmentFile(
            $site = $this->forge->site($server->id, $siteId)
        );

        abort_unless(
            File::exists($file),
            1,
            'The environment variables for that site have not been downloaded.'
        );

        if (is_null($this->argument('file')) && ! $this->confirmStep(
            ['Would You Like Update The Site Environment File With The Contents Of The File %s', basename($file)]
        )) {
            return 0;
        }

        $this->step(['Uploading %s Environment File', basename($file)]);

        $this->forge->updateSiteEnvironmentFile(
            $this->currentServer()->id,
            $site->id,
            File::get($file)
        );

        $this->successfulStep(['Environment variables uploaded successfully to %s', $site->name]);
        $this->step('You may need to deploy the site for the new variables to take effect.');

        if (is_null($this->argument('file')) && $this->confirmStep(['Would you like to delete the environment file %s from your machine', basename($file)])) {
            File::delete($file);
        }
    }
}
