<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Modifier;

use Ghostwriter\Handrail\File\IncludedFileInterface;
use Ghostwriter\Handrail\File\ModifiedFileInterface;

interface ModifierInterface
{
    public function modify(IncludedFileInterface $file): ModifiedFileInterface;
}
