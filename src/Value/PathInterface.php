<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Value;

use Stringable;

interface PathInterface extends Stringable
{
    /**
     * @param non-empty-string $path
     */
    public static function new(string $path): self;
}
