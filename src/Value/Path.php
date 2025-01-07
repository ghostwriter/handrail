<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Value;

use Override;

final class Path implements PathInterface
{
    /**
     * @var array<non-empty-string,self>
     */
    private static array $instances = [];

    /**
     * @param non-empty-string $path
     */
    private function __construct(
        private readonly string $path,
    ) {}

    /**
     * @param non-empty-string $path
     */
    #[Override]
    public static function new(string $path): self
    {
        return self::$instances[$path] ??= new self($path);
    }

    public function __destruct()
    {
        unset(self::$instances[$this->path]);
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
