<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Value;

use Ghostwriter\Handrail\Modifier\ModifierInterface;
use Ghostwriter\Handrail\Value\File\ModifiedFileInterface;
use Stringable;

interface FileInterface extends Stringable
{
    public function modify(ModifierInterface $modifier): ModifiedFileInterface;

    public function path(): PathInterface;
}
