<?php

namespace App\Support;

use Throwable;

class TtyReader
{
    /**
     * Read input by invoking the given reader. When STDIN is a TTY on a POSIX
     * system, the kernel's canonical line discipline caps a single line at
     * MAX_CANON (~1024 bytes), silently truncating longer pasted input. We
     * temporarily switch the terminal to non-canonical mode for the duration
     * of the read so long tokens are accepted, then restore the previous
     * terminal state — even if the reader throws.
     *
     * @param  callable():mixed  $reader
     * @return string
     */
    public function read(callable $reader)
    {
        if (! $this->shouldDisableCanonicalMode()) {
            return (string) $reader();
        }

        $previousState = $this->captureState();

        if ($previousState === null) {
            return (string) $reader();
        }

        try {
            $this->disableCanonicalMode();

            return (string) $reader();
        } finally {
            $this->restoreState($previousState);
        }
    }

    /**
     * Determine whether the terminal can be switched to non-canonical mode.
     *
     * @return bool
     */
    protected function shouldDisableCanonicalMode()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return false;
        }

        if (! defined('STDIN') || ! function_exists('posix_isatty')) {
            return false;
        }

        try {
            if (! @posix_isatty(STDIN)) {
                return false;
            }
        } catch (Throwable $e) {
            return false;
        }

        return $this->sttyAvailable();
    }

    /**
     * Determine whether the `stty` binary is available on this system.
     *
     * @return bool
     */
    protected function sttyAvailable()
    {
        [$exitCode] = $this->runStty(['-g']);

        return $exitCode === 0;
    }

    /**
     * Capture the current terminal state, suitable for later restoration.
     *
     * @return string|null
     */
    protected function captureState()
    {
        [$exitCode, $stdout] = $this->runStty(['-g']);

        if ($exitCode !== 0 || $stdout === '') {
            return null;
        }

        return trim($stdout);
    }

    /**
     * Switch the terminal into non-canonical mode (no line buffering / cap).
     *
     * @return void
     */
    protected function disableCanonicalMode()
    {
        $this->runStty(['-icanon', 'min', '1']);
    }

    /**
     * Restore the terminal to a previously captured state.
     *
     * @param  string  $state
     * @return void
     */
    protected function restoreState($state)
    {
        $this->runStty([$state]);
    }

    /**
     * Invoke the `stty` binary with the given arguments via proc_open so we
     * never go through a shell (no quoting, no injection surface).
     *
     * @param  array<int, string>  $args
     * @return array{0: int, 1: string}  [exit code, stdout]
     */
    protected function runStty(array $args)
    {
        $descriptors = [
            0 => ['file', '/dev/tty', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open(
            array_merge(['stty'], $args),
            $descriptors,
            $pipes
        );

        if (! is_resource($process)) {
            return [1, ''];
        }

        $stdout = stream_get_contents($pipes[1]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return [$exitCode, $stdout];
    }
}
