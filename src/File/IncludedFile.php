<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\File;

use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\Modifier\ModifierInterface;
use Ghostwriter\Handrail\PathInterface;
use Override;
use Throwable;

final readonly class IncludedFile implements IncludedFileInterface
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        private PathInterface $path,
        private string $code,
    ) {
    }

    #[Override]
    public static function new(PathInterface $path, string $code): self
    {
        return new self($path, $code);
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function __toString(): string
    {
        return $this->path->__toString();
    }

    #[Override]
    public function code(): string
    {
        return $this->code;
    }

    #[Override]
    public function delete(FilesystemInterface $filesystem): DeletedFileInterface
    {
        $filesystem->delete($this->path);

        return DeletedFile::new($this->path);
    }

    /**
     * @throws Throwable
     */
    #[Override]
    public function modify(ModifierInterface $modifier): ModifiedFileInterface
    {
        return $modifier->modify($this);
    }

    #[Override]
    public function path(): PathInterface
    {
        return $this->path;
    }
}
