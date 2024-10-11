<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Value\File;

use Ghostwriter\Filesystem\Interface\FilesystemInterface;

interface ModifiedFileInterface extends OriginalFileInterface
{
    public function save(FilesystemInterface $filesystem): OriginalFileInterface;
}
