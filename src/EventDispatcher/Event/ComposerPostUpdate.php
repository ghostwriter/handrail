<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Event;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;

final class ComposerPostUpdate
{
    public function __construct(
        private Event $event,
    ) {
    }

    public function event(): Event
    {
        return $this->event;
    }

    public function getComposer(): Composer
    {
        return $this->event->getComposer();
    }

    public function getIO(): IOInterface
    {
        return $this->event->getIO();
    }
}
