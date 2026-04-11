<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Event;

use Composer\Composer;
use Composer\IO\IOInterface;

final readonly class ComposerPluginActivateEvent
{
    public function __construct(
        private Composer $composer,
        private IOInterface $io
    ) {}

    public function composer(): Composer
    {
        return $this->composer;
    }

    public function io(): IOInterface
    {
        return $this->io;
    }
}
