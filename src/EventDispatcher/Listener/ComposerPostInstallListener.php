<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Listener;

use Composer\Console\Application;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostInstall;
use Symfony\Component\Console\Input\ArrayInput;

final readonly class ComposerPostInstallListener
{
    public function __construct(
        private Application $application
    ) {}

    public function __invoke(ComposerPostInstall $composerPostInstall): void
    {
        $this->application->run(new ArrayInput([
            'command' => 'handrail',
        ]));
    }
}
