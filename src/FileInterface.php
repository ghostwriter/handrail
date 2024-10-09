<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

use Stringable;

interface FileInterface extends Stringable
{
    public function path(): PathInterface;
}
