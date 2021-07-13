<?php

namespace App\Exceptions;

use Exception;
use NunoMaduro\Collision\Contracts\RenderlessEditor;
use NunoMaduro\Collision\Contracts\RenderlessTrace;

class UnauthorizedException extends Exception implements RenderlessEditor, RenderlessTrace
{
    // ..
}
