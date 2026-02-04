<?php

namespace App\Commands;

class CompletionCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'completion
        {shell=zsh : Shell type (zsh, bash)}
        {--install : Add completion to your shell config}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate shell completion script';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shell = $this->argument('shell');

        if ($shell !== 'zsh') {
            $this->error("Only 'zsh' is currently supported. Bash coming soon.");
            return 1;
        }

        $completionFile = $this->getCompletionPath();

        if (! file_exists($completionFile)) {
            $this->error("Completion file not found: {$completionFile}");
            return 1;
        }

        if ($this->option('install')) {
            return $this->installCompletion($completionFile);
        }

        // Output the completion script
        $this->line(file_get_contents($completionFile));
        $this->line('');
        $this->comment('# Add to your ~/.zshrc:');
        $this->comment("#   source {$completionFile}");
        $this->comment('# Or run: forge completion --install');

        return 0;
    }

    /**
     * Get the path to the completion script.
     *
     * @return string
     */
    protected function getCompletionPath()
    {
        return dirname(__DIR__, 2) . '/completions/forge.zsh';
    }

    /**
     * Install the completion to the user's shell config.
     *
     * @param  string  $completionFile
     * @return int
     */
    protected function installCompletion($completionFile)
    {
        $zshrc = ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE']) . '/.zshrc';

        if (! file_exists($zshrc)) {
            $this->error('.zshrc not found. Please add manually:');
            $this->line("  source {$completionFile}");
            return 1;
        }

        $contents = file_get_contents($zshrc);
        $sourceLine = "source {$completionFile}";

        // Check if already installed
        if (str_contains($contents, 'completions/forge.zsh')) {
            $this->warnStep('Forge completion already installed in ~/.zshrc');
            return 0;
        }

        // Add to zshrc
        $addition = "\n# Forge CLI completion\n{$sourceLine}\n";
        file_put_contents($zshrc, $contents . $addition);

        $this->successfulStep('Completion installed to ~/.zshrc');
        $this->line('');
        $this->line('  Restart your terminal or run:');
        $this->line("  <comment>source ~/.zshrc</comment>");

        return 0;
    }
}
