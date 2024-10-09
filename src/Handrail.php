<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

use Composer\Composer;
use Composer\IO\IOInterface;
use Ghostwriter\Container\Attribute\Inject;
use Ghostwriter\Container\Container;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\Container\ServiceProvider;
use Ghostwriter\Handrail\Exception\ShouldNotHappenException;
use Ghostwriter\Handrail\File\IncludedFileInterface;
use Ghostwriter\Handrail\File\ModifiedFileInterface;
use Ghostwriter\Handrail\Modifier\FileModifier;
use Ghostwriter\Handrail\Paths\ExcludePaths;
use Ghostwriter\Handrail\Paths\IncludePaths;
use Override;
use SplFileInfo;
use Throwable;

use const PHP_EOL;
use const STDOUT;

/** @see HandrailTest */
final readonly class Handrail implements HandrailInterface
{
    public function __construct(
        private IncludePaths $includePaths,
        private ExcludePaths $excludePaths,
        #[Inject(Filesystem::class)]
        private FilesystemInterface $filesystem,
        private FileModifier $fileModifier,
    ) {
    }

    /**
     * @param list<string> $includePaths
     * @param list<string> $excludePaths
     *
     * @throws Throwable
     */
    public static function new(array $includePaths = [], array $excludePaths = []): self
    {
        $container = Container::getInstance();

        if (! $container->has(ServiceProvider::class)) {
            $container->provide(ServiceProvider::class);
        }

        $self = $container->get(self::class);
        $self->exclude(...$excludePaths);
        $self->include(...$includePaths);
        return $self;
    }

    /**
     * @throws Throwable
     */
    public function exclude(string ...$paths): self
    {
        foreach ($paths as $path) {
            match (true) {
                $this->filesystem->isDirectory($path) => $this->excludeDirectory($path),
                $this->filesystem->isFile($path) => $this->excludeFile($path),
                default => throw new ShouldNotHappenException(\sprintf('Path is not a file or directory: %s', $path)),
            };
        }

        return $this;
    }

    /**
     * @throws Throwable
     */
    #[Override]
    public function guard(string $path): void
    {
        match (true) {
            $this->filesystem->isDirectory($path) => $this->guardDirectory($path),
            $this->filesystem->isFile($path) => $this->guardFile($path),
            default => throw new ShouldNotHappenException(\sprintf('Path is not a file or directory: %s', $path)),
        };

        /** @var IncludedFileInterface[] $phpFiles */
        $phpFiles = \array_diff($this->includePaths->toArray(), $this->excludePaths->toArray());

        foreach ($phpFiles as &$phpFile) {
            $modifiedPhpFile = $phpFile->modify($this->fileModifier);

            if ($modifiedPhpFile->code() === $phpFile->code()) {
                continue;
            }

            if (! $modifiedPhpFile instanceof ModifiedFileInterface) {
                return;
            }

            $modifiedPhpFile->save($this->filesystem);
        }
    }

    /**
     * @throws Throwable
     */
    public function include(string ...$paths): self
    {
        foreach ($paths as $path) {
            match (true) {
                $this->filesystem->isDirectory($path) => $this->includeDirectory($path),
                $this->filesystem->isFile($path) => $this->includeFile($path),
                default => throw new ShouldNotHappenException(
                    \sprintf('Path is not a file or directory: %s', $path)
                ),
            };
        }

        return $this;
    }

    /**
     * @throws Throwable
     */
    private function excludeDirectory(string $path): void
    {
        foreach ($this->filesystem->recursiveRegexIterator(
            $this->filesystem->recursiveDirectoryIterator($path),
            '#^.+\.php$#i'
        ) as $splFileInfo) {
            if (! $splFileInfo instanceof SplFileInfo) {
                continue;
            }

            $this->excludeFile($splFileInfo->getPathname());
        }
    }

    /**
     * @param non-empty-string $path
     *
     * @throws Throwable
     */
    private function excludeFile(string $path): void
    {
        $this->excludePaths->add(Path::new($path));
    }

    /**
     * @throws Throwable
     */
    private function guardDirectory(string $directory): void
    {
        foreach ($this->filesystem->recursiveRegexIterator(
            $this->filesystem->recursiveDirectoryIterator($directory),
            '#^.+\.php$#i'
        ) as $splFileInfo) {
            if (! $splFileInfo instanceof SplFileInfo) {
                continue;
            }

            $path = Path::new($splFileInfo->getPathname());

            if (
                $this->includePaths->contains($path) ||
                $this->excludePaths->contains($path)
            ) {
                continue;
            }

            $this->includePaths->add($path);
        }
    }

    /**
     * @param non-empty-string $path
     *
     * @throws Throwable
     */
    private function guardFile(string $path): void
    {
        $path = Path::new($path);

        if (
            $this->includePaths->contains($path) ||
            $this->excludePaths->contains($path)
        ) {
            return;
        }

        $this->includePaths->add($path);
    }

    /**
     * @throws Throwable
     */
    private function includeDirectory(string $directory): void
    {
        foreach ($this->filesystem->recursiveRegexIterator(
            $this->filesystem->recursiveDirectoryIterator($directory),
            '#^.+\.php$#i'
        ) as $splFileInfo) {
            if (! $splFileInfo instanceof SplFileInfo) {
                continue;
            }

            $this->includeFile($splFileInfo->getPathname());
        }
    }

    /**
     * @param non-empty-string $path
     *
     * @throws Throwable
     */
    private function includeFile(string $path): void
    {
        $this->includePaths->add(Path::new($path));
    }

    public static function activate(Composer $composer, IOInterface $io): void
    {
        \fwrite(STDOUT, self::class . ' - ' . __METHOD__ . PHP_EOL);
    }

    public static function deactivate(Composer $composer, IOInterface $io): void
    {
        \fwrite(STDOUT, self::class . ' - ' . __METHOD__ . PHP_EOL);
    }

    public static function postInstall(\Composer\Script\Event $event): void
    {
        \fwrite(STDOUT, self::class . ' - ' . $event::class . ' - ' . __METHOD__ . PHP_EOL);
    }

    public static function postUpdate(\Composer\Script\Event $event): void
    {
        \fwrite(STDOUT, self::class . ' - ' . $event::class . ' - ' . __METHOD__ . PHP_EOL);
    }

    public static function uninstall(Composer $composer, IOInterface $io): void
    {
        \fwrite(STDOUT, self::class . ' - ' . __METHOD__ . PHP_EOL);
    }
}
