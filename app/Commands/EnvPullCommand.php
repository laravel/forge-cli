<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

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

        $organization = $this->currentOrganization();
        $server = $this->currentServer();
        $site = spin(
            fn () => $this->forge->organizationSite($organization, (int) $siteId),
            'Retrieving site',
        );
        $file = $this->getEnvironmentFile($site);

        if (is_null($this->argument('file')) && File::exists($file) && ! confirm(
            'File already exists with the name '.basename($file).'. Would you like to overwrite it?'
        )) {
            return 0;
        }

        File::delete($file);

        File::put(
            $file,
            spin(
                fn () => $this->forge->siteEnvironment($organization, $server->id, $site->id),
                'Downloading environment file',
            ),
        );

        info('Environment variables written to '.basename($file));
    }
}
