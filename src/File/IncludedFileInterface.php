<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\File;

use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\FileInterface;
use Ghostwriter\Handrail\Modifier\ModifierInterface;
use Ghostwriter\Handrail\PathInterface;

interface IncludedFileInterface extends FileInterface
{
    public static function new(PathInterface $path, string $code): self;

    public function code(): string;

    public function delete(FilesystemInterface $filesystem): DeletedFileInterface;

    public function modify(ModifierInterface $modifier): ModifiedFileInterface;
}
