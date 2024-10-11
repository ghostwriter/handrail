<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Value\File;

use Ghostwriter\Handrail\Value\FileInterface;
use Ghostwriter\Handrail\Value\PathInterface;

interface OriginalFileInterface extends FileInterface
{
    public static function new(PathInterface $path, string $code): self;

    public function code(): string;
}
