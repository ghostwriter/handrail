<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Container\Factory;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\FactoryInterface;
use Ghostwriter\EventDispatcher\ListenerProvider;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginActivate;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginDeactivate;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginUninstall;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostInstall;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostUpdate;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPluginActivateListener;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPluginDeactivateListener;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPluginUninstallListener;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPostInstallListener;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPostUpdateListener;

/**
 * @implements FactoryInterface<ListenerProvider>
 */
final class ListenerProviderFactory implements FactoryInterface
{
    /**
     * @return ListenerProvider
     */
    public function __invoke(ContainerInterface $container): object
    {
        $listenerProvider = new ListenerProvider($container);
        $listenerProvider->bind(ComposerPluginActivate::class, ComposerPluginActivateListener::class);
        $listenerProvider->bind(ComposerPluginDeactivate::class, ComposerPluginDeactivateListener::class);
        $listenerProvider->bind(ComposerPluginUninstall::class, ComposerPluginUninstallListener::class);

        $listenerProvider->bind(ComposerPostInstall::class, ComposerPostInstallListener::class);
        $listenerProvider->bind(ComposerPostUpdate::class, ComposerPostUpdateListener::class);

        return $listenerProvider;
    }
}
