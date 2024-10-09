<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\File;

use Ghostwriter\Handrail\FileInterface;
use Ghostwriter\Handrail\PathInterface;

interface ExcludedFileInterface extends FileInterface
{
    public static function new(PathInterface $path): self;
}
