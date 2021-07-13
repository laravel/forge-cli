<?php

namespace App\Exceptions;

use InvalidArgumentException;
use NunoMaduro\Collision\Contracts\RenderlessEditor;
use NunoMaduro\Collision\Contracts\RenderlessTrace;

class NotFoundException extends InvalidArgumentException implements RenderlessEditor, RenderlessTrace
{
    // ..
}
