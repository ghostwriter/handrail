<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Event;

use Composer\Composer;
use Composer\IO\IOInterface;

final class ComposerPluginDeactivate
{
    public function __construct(
        private Composer $composer,
        private IOInterface $io
    ) {
    }

    public function getComposer(): Composer
    {
        return $this->composer;
    }

    public function getIO(): IOInterface
    {
        return $this->io;
    }
}
