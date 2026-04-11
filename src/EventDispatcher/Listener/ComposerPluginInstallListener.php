<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Listener;

use Composer\Console\Application;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginInstallEvent;
use Symfony\Component\Console\Input\ArrayInput;

final readonly class ComposerPluginInstallListener
{
    public function __construct(
        private Application $application
    ) {}

    public function __invoke(ComposerPluginInstallEvent $composerPluginInstallEvent): void
    {
        $this->application->run(new ArrayInput([
            'command' => 'handrail',
        ]));
    }
}
