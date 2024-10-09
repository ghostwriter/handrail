<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Paths;

use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\File\IncludedFile;
use Ghostwriter\Handrail\File\IncludedFileInterface;
use Ghostwriter\Handrail\FileInterface;
use Ghostwriter\Handrail\Path;
use Ghostwriter\Handrail\PathInterface;
use WeakMap;

final class IncludePaths
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

    public static function new(FilesystemInterface $filesystem, string ...$paths): self
    {
        return new self(
            $filesystem,
            ...\array_map(
                static fn (string $path): IncludedFileInterface
                    => IncludedFile::new(Path::new($path), Filesystem::new()->read($path)),
                $paths
            )
        );
    }

    public function add(PathInterface $path): void
    {
        $this->weakMap->offsetSet($path, IncludedFile::new($path, $this->filesystem->read((string) $path)));
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