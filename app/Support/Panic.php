<?php

namespace App\Support;

class Panic
{
    /**
     * Abort in unexpected issues.
     *
     * @param  string  $message
     * @return never
     */
    public static function abort($message)
    {
        abort(1, sprintf(<<<'EOF'
            An unexpected error occured. Please report this issue here: https://github.com/laravel/forge-cli.
            - Issue: %s.
            - PHP version: %s.
            - Operating system: %s.
            EOF
        , $message, phpversion(), PHP_OS));
    }
}
