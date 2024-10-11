<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Value\File;

use Ghostwriter\Handrail\Modifier\ModifierInterface;
use Ghostwriter\Handrail\Value\PathInterface;
use Override;
use Throwable;

final readonly class OriginalFile implements OriginalFileInterface
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
