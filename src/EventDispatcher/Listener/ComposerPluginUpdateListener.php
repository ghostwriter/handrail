<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Listener;

use Composer\Console\Application;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginUpdateEvent;
use Symfony\Component\Console\Input\ArrayInput;

final readonly class ComposerPluginUpdateListener
{
    public function __construct(
        private Application $application
    ) {}

    public function __invoke(ComposerPluginUpdateEvent $composerPluginUpdateEvent): void
    {
        $this->application->run(new ArrayInput([
            'command' => 'handrail',
        ]));
    }
}
