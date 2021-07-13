<?php

namespace App\Exceptions;

use LogicException as BaseLogicException;
use NunoMaduro\Collision\Contracts\RenderlessEditor;
use NunoMaduro\Collision\Contracts\RenderlessTrace;

class LogicException extends BaseLogicException implements RenderlessEditor, RenderlessTrace
{
    // ..
}
