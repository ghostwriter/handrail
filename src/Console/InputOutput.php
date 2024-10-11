<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Console;

use Ghostwriter\Handrail\Handrail;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use const PHP_EOL;

final readonly class InputOutput
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    public function catch(Throwable $throwable): void
    {
        $this->symfonyStyle->warning(
            \sprintf(
                '[%s]: %s %s%s',
                Handrail::PACKAGE_NAME,
                \mb_substr(\mb_strrchr($throwable::class, '\\'), 1) . ' was thrown:',
                PHP_EOL,
                $throwable->getMessage(),
            )
        );
    }

    public function error(string $message): void
    {
        $this->symfonyStyle->error(\sprintf('[%s]: %s', Handrail::PACKAGE_NAME, $message));
    }

    public function info(string $message): void
    {
        $this->symfonyStyle->info(\sprintf('[%s]: %s', Handrail::PACKAGE_NAME, $message));
    }

    public function iterate(iterable $iterables): iterable
    {
        yield from $this->symfonyStyle->progressIterate($iterables);
    }

    public function success(string $message): void
    {
        $this->symfonyStyle->success(\sprintf('[%s]: %s', Handrail::PACKAGE_NAME, $message));
    }

    public function title(string $message): void
    {
        $this->symfonyStyle->title(\sprintf('[%s]', $message));
    }

    public function warning(string $message): void
    {
        $this->symfonyStyle->warning(\sprintf('[%s]: %s', Handrail::PACKAGE_NAME, $message));
    }
}
