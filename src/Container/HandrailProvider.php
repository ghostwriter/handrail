<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Container;

use Ghostwriter\Container\Interface\BuilderInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Ghostwriter\EventDispatcher\Interface\ListenerProviderInterface;
use Ghostwriter\Handrail\Handrail;
use Ghostwriter\Handrail\HandrailInterface;
use Override;
use Throwable;

/**
 * @see HandrailProviderTest
 */
final class HandrailProvider extends AbstractProvider
{
    /** @throws Throwable */
    #[Override]
    public function register(BuilderInterface $builder): void
    {
        $builder->alias(HandrailInterface::class, Handrail::class);
        $builder->extend(ListenerProviderInterface::class, ListenerProviderExtension::class);
    }
}
