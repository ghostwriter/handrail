<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\EventDispatcher\Listener;

use function var_dump;

final readonly class DebugListener
{
    public function __invoke(object $event): void
    {
        var_dump($event::class);
    }
}
