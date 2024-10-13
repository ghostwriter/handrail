<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Value;

use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\Value\File\OriginalFile;
use Ghostwriter\Handrail\Value\File\OriginalFileInterface;
use WeakMap;

final readonly class Paths
{
    /**
     * @var WeakMap<PathInterface,FileInterface>
     */
    private WeakMap $weakMap;

    public function __construct(
        private FilesystemInterface $filesystem,
        FileInterface ...$files
    ) {
        /** @var WeakMap<PathInterface,FileInterface> $weakMap */
        $weakMap = new WeakMap();

        foreach ($files as $file) {
            $weakMap->offsetSet($file->path(), $file);
        }

        $this->weakMap = $weakMap;
    }

    public static function new(FilesystemInterface $filesystem, string ...$files): self
    {
        return new self(
            $filesystem,
            ...\array_map(
                static fn (string $path): OriginalFileInterface
                    => OriginalFile::new(Path::new($path), Filesystem::new()->read($path)),
                $files
            )
        );
    }

    public function add(PathInterface $path): void
    {
        $this->weakMap->offsetSet($path, OriginalFile::new($path, $this->filesystem->read($path->__toString())));
    }

    public function contains(PathInterface $path): bool
    {
        return $this->weakMap->offsetExists($path);
    }

    /**
     * @return array<string,FileInterface>
     */
    public function toArray(): array
    {
        $all = [];

        foreach ($this->weakMap as $path => $file) {
            $all[$path->__toString()] = $file;
        }

        return $all;
    }
}
