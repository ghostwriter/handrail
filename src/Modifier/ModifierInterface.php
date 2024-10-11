<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Modifier;

use Ghostwriter\Handrail\Value\File\ModifiedFileInterface;
use Ghostwriter\Handrail\Value\File\OriginalFileInterface;

interface ModifierInterface
{
    public function modify(OriginalFileInterface $originalFile): ModifiedFileInterface;
}
