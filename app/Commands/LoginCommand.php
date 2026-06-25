<?php

namespace App\Commands;

use App\Support\TtyReader;

class LoginCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'login
        {--token= : Forge API token}
        {--token-file= : Path to a file containing the Forge API token (recommended for long tokens)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Authenticate with Laravel Forge (accepts --token, --token-file, the FORGE_API_TOKEN env var, or an interactive prompt)';

    /**
     * Execute the console command.
     *
     * @param  \App\Support\TtyReader  $tty
     * @return void
     */
    public function handle(TtyReader $tty)
    {
        [$token, $source] = $this->resolveToken($tty);

        $token = trim((string) $token);

        abort_if($token === '', 1, 'A Forge API token is required.');

        if ($source === 'prompt' && strlen($token) >= 1023) {
            $this->warnStep(
                'The pasted token may have been truncated by the terminal (input was 1023+ bytes). '.
                'Re-run with <comment>--token-file=PATH</comment> or set <comment>FORGE_API_TOKEN</comment>.'
            );
        }

        $this->config->set('token', $token);

        $email = $this->getUserEmail();

        $this->ensureCurrentTeamIsSet();

        $this->successfulStep("Authenticated successfully as <comment>[$email]</comment>");
    }

    /**
     * Resolve the API token from the highest-priority available source.
     *
     * Precedence: --token > --token-file > FORGE_API_TOKEN > interactive prompt.
     *
     * @param  \App\Support\TtyReader  $tty
     * @return array{0: string, 1: string}  [token, source]
     */
    protected function resolveToken(TtyReader $tty)
    {
        $flag = $this->option('token');

        if ($flag !== null && $flag !== '') {
            return [$flag, 'flag'];
        }

        $file = $this->option('token-file');

        if ($file !== null && $file !== '') {
            return [$this->readTokenFile($file), 'file'];
        }

        $env = getenv('FORGE_API_TOKEN');

        if ($env !== false && $env !== '') {
            return [$env, 'env'];
        }

        $prompted = $tty->read(function () {
            return $this->askStep('Please enter your Forge API token');
        });

        return [$prompted, 'prompt'];
    }

    /**
     * Read the token from the given file path.
     *
     * @param  string  $path
     * @return string
     */
    protected function readTokenFile($path)
    {
        abort_if(! is_file($path) || ! is_readable($path), 1, "Unable to read token file: [{$path}]");

        $contents = @file_get_contents($path);

        abort_if($contents === false, 1, "Unable to read token file: [{$path}]");

        return $contents;
    }

    /**
     * Gets user's email.
     *
     * @return string
     */
    protected function getUserEmail()
    {
        return $this->forge->user()->email;
    }
}
