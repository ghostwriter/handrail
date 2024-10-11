<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Event;

use Composer\Script\Event;

final readonly class ComposerPostInstall
{
    public function __construct(
        private Event $event,
    ) {
    }

    public function event(): Event
    {
        return $this->event;
    }
}
