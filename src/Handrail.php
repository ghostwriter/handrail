<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

use Ghostwriter\Container\Container;
use Ghostwriter\EventDispatcher\Interface\EventDispatcherInterface;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\Container\ServiceProvider;
use Ghostwriter\Handrail\Exception\ShouldNotHappenException;
use Ghostwriter\Handrail\Modifier\FunctionDeclarationModifier;
use Ghostwriter\Handrail\Value\File\OriginalFileInterface;
use Ghostwriter\Handrail\Value\Path;
use Ghostwriter\Handrail\Value\Paths;
use Override;
use Throwable;

/** @see HandrailTest */
final readonly class Handrail implements HandrailInterface
{
    public const string PACKAGE_NAME = 'ghostwriter/handrail';

    public const string OPTION_FILES = 'files';

    public const string OPTION_EXCLUDE = 'exclude';

    public const string OPTION_INCLUDE = 'include';

    public const string OPTION_DISABLE = 'disable';

    public const string EXTRA = 'extra';

    public function __construct(
        private Paths $paths,
        private FilesystemInterface $filesystem,
        private FunctionDeclarationModifier $functionDeclarationModifier,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public static function new(): self
    {
        $container = Container::getInstance();

        if (! $container->has(ServiceProvider::class)) {
            $container->provide(ServiceProvider::class);
        }

        return $container->get(self::class);
    }

    /**
     * @param non-empty-string ...$files
     *
     * @throws Throwable
     */
    #[Override]
    public function guard(string ...$files): void
    {
        foreach ($files as $file) {
            if (! $this->filesystem->isFile($file)) {
                throw new ShouldNotHappenException(\sprintf('Path is not a file: %s', $file));
            }

            $this->guardFile($file);
        }

        /** @var OriginalFileInterface $includedFile */
        foreach ($this->paths->toArray() as $includedFile) {
            $modifiedFile = $includedFile->modify($this->functionDeclarationModifier);

            $modifiedFile->save($this->filesystem);
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

        if ($this->paths->contains($path)) {
            return;
        }

        $this->paths->add($path);
    }
}
