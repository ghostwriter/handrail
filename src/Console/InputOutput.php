<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Console;

use Symfony\Component\Console\Style\SymfonyStyle;

final class InputOutput
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    public function error(string $message): void
    {
        $this->symfonyStyle->error(\sprintf('[%s]: %s', 'ghostwriter/handrail', $message));
    }

    public function iterate(iterable $iterables): iterable
    {
        yield from $this->symfonyStyle->progressIterate($iterables);
    }

    public function success(string $message): void
    {
        $this->symfonyStyle->success(\sprintf('[%s]: %s', 'ghostwriter/handrail', $message));
    }

    public function title(string $message): void
    {
        $this->symfonyStyle->title(\sprintf('[%s]', $message));
    }

    public function warning(string $message): void
    {
        $this->symfonyStyle->warning(\sprintf('[%s]: %s', 'ghostwriter/handrail', $message));
    }
}
