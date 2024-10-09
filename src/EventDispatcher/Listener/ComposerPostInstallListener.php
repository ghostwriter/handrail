<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Listener;

use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostInstall;

use const PHP_EOL;
use const STDOUT;

final class ComposerPostInstallListener
{
    public function __invoke(ComposerPostInstall $event): void
    {
        $eventName = \mb_substr(\mb_strrchr($event::class, '\\'), 1);
        $listenerName = \mb_substr(\mb_strrchr(self::class, '\\'), 1);

        \fwrite(STDOUT, $listenerName . ' - ' . $eventName . PHP_EOL);

        $event->getIO()
            ->write('Composer post install event' . PHP_EOL);
    }
}
