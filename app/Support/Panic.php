<?php

namespace App\Support;

use Throwable;

class Panic
{
    /**
     * Abort in unexpected issues.
     *
     * @param  \Throwable|string  $message
     * @return never
     */
    public static function abort($message)
    {
        if ($message instanceof Throwable) {
            $message = $message->getMessage();
        }

        abort(1, sprintf(<<<'EOF'
            An unexpected error occured. Please report this issue here:
            https://github.com/laravel/forge-cli/issues/new/choose

            - Forge CLI Version: %s
            - PHP Version: %s
            - Operating System: %s
            - Error Message: %s.
            EOF
        , config('app.version'), phpversion(), PHP_OS, $message));
    }
}
