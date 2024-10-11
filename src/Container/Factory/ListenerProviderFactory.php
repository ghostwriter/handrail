<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Container\Factory;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\FactoryInterface;
use Ghostwriter\EventDispatcher\ListenerProvider;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostInstall;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostUpdate;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPostInstallListener;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPostUpdateListener;
use Override;
use Throwable;

/**
 * @implements FactoryInterface<ListenerProvider>
 */
final readonly class ListenerProviderFactory implements FactoryInterface
{
    /**
     * @throws Throwable
     *
     * @return ListenerProvider
     */
    #[Override]
    public function __invoke(ContainerInterface $container): object
    {
        $listenerProvider = new ListenerProvider($container);

        $listenerProvider->bind(ComposerPostInstall::class, ComposerPostInstallListener::class);
        $listenerProvider->bind(ComposerPostUpdate::class, ComposerPostUpdateListener::class);

        return $listenerProvider;
    }
}
