<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

use Override;

final readonly class Path implements PathInterface
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        private string $path,
    ) {
    }

    /**
     * @param non-empty-string $path
     */
    public static function new(string $path): self
    {
        return new self($path);
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function __toString(): string
    {
        return $this->path;
    }
}
