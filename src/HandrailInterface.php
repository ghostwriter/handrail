<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

interface HandrailInterface
{
    public function guard(string ...$files): void;
}
