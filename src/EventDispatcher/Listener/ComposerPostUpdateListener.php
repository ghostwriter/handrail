<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Listener;

use Composer\Console\Application;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostUpdate;
use Symfony\Component\Console\Input\ArrayInput;

final readonly class ComposerPostUpdateListener
{
    public function __construct(
        private Application $application
    ) {}

    public function __invoke(ComposerPostUpdate $composerPostUpdate): void
    {
        $this->application->run(new ArrayInput([
            'command' => 'handrail',
        ]));
    }
}
