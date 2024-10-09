<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\File;

use Ghostwriter\Handrail\PathInterface;
use Override;
use Throwable;

final readonly class ExcludedFile implements ExcludedFileInterface
{
    public function __construct(
        private PathInterface $path,
    ) {
    }

    /**
     * @throws Throwable
     */
    #[Override]
    public static function new(PathInterface $path): self
    {
        return new self($path);
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
    public function path(): PathInterface
    {
        return $this->path;
    }
}
