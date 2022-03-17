<?php

namespace App\Commands;

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

        if (! is_dir($path)) {
            mkdir($path);
        }

        $gitignore = getcwd() . '/.gitignore';

        if (is_writeable($gitignore)) {
            $this->updateGitignore($gitignore);
        }

        $this->successfulStep("Initialized Successfully.");
    }

    /**
     * Update the project's .gitignore file.
     *
     * @param  string  $path
     * @return void
     */
    protected function updateGitignore(string $path)
    {
        $contents = file_get_contents($path);
        $contents .= '.laravel-forge' . PHP_EOL;

        file_put_contents($path, $contents);
    }
}
