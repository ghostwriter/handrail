<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\File;

use Ghostwriter\Handrail\PathInterface;

interface DeletedFileInterface extends ExcludedFileInterface
{
    public static function new(PathInterface $path): self;
}
