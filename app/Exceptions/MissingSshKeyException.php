<?php

namespace App\Exceptions;

use LogicException as BaseLogicException;
use NunoMaduro\Collision\Contracts\RenderlessEditor;
use NunoMaduro\Collision\Contracts\RenderlessTrace;

class MissingSshKeyException extends BaseLogicException implements RenderlessEditor, RenderlessTrace
{
    /**
     * Raise the exception.
     *
     * @return never
     */
    public static function raise()
    {
        throw new self('Unable to connect to remove server. Have you configured an SSH Key?');
    }
}
