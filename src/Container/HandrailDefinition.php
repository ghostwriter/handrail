<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Container;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\DefinitionInterface;
use Ghostwriter\EventDispatcher\ListenerProvider;
use Ghostwriter\Handrail\Handrail;
use Ghostwriter\Handrail\HandrailInterface;
use Override;
use Throwable;

final readonly class HandrailDefinition implements DefinitionInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): void
    {
        $container->alias(Handrail::class, HandrailInterface::class);

        $container->extend(ListenerProvider::class, ListenerProviderExtension::class);
    }
}
