<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\File;

use Ghostwriter\Filesystem\Interface\FilesystemInterface;

interface ModifiedFileInterface extends IncludedFileInterface
{
    public function save(FilesystemInterface $filesystem): IncludedFileInterface;
}
