<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;

class InitCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Initialise the Forge CLI in the current directory.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $path = getcwd() . '/.laravel-forge';

        if (! File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        $gitignore = getcwd() . '/.gitignore';

        if (File::isWritable($gitignore)) {
            File::append($gitignore, '.laravel-forge' . PHP_EOL);
        }

        $this->successfulStep("Initialized Successfully");
    }

    /**
     * Update the project's .gitignore file.
     *
     * @param  string  $path
     * @return void
     */
    protected function updateGitignore(string $path)
    {

    }
}
