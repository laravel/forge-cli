<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

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

        $organization = $this->currentOrganization();
        $server = $this->currentServer();
        $site = spin(
            fn () => $this->forge->organizationSite($organization, (int) $siteId),
            'Retrieving site',
        );
        $file = $this->getEnvironmentFile($site);

        abort_unless(
            File::exists($file),
            1,
            'The environment variables for that site have not been downloaded.'
        );

        if (is_null($this->argument('file')) && ! confirm(
            'Would you like to update the site environment file with the contents of '.basename($file).'?'
        )) {
            return 0;
        }

        spin(
            fn () => $this->forge->updateSiteEnvironment(
                $organization,
                $server->id,
                $site->id,
                File::get($file),
            ),
            'Uploading '.basename($file),
        );

        info('Environment variables uploaded successfully to '.$site->name);
        info('You may need to deploy the site for the new variables to take effect.');

        if (is_null($this->argument('file')) && confirm('Would you like to delete the environment file '.basename($file).' from your machine?')) {
            File::delete($file);
        }
    }
}
