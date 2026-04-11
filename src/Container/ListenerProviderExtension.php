<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Container;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\ExtensionInterface;
use Ghostwriter\EventDispatcher\ListenerProvider;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginActivateEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginDeactivateEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginInstallEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginUninstallEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginUpdateEvent;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPluginInstallListener;
use Ghostwriter\Handrail\EventDispatcher\Listener\ComposerPluginUpdateListener;
use Ghostwriter\Handrail\EventDispatcher\Listener\DebugListener;
use Override;
use Throwable;

use function assert;

/**
 * @see ListenerProviderExtensionTest
 *
 * @implements ExtensionInterface<ListenerProvider>
 */
final readonly class ListenerProviderExtension implements ExtensionInterface
{
    private const array EVENTS = [
        // 'object' => [DebugListener::class],
        ComposerPluginActivateEvent::class => [],
        ComposerPluginDeactivateEvent::class => [],
        ComposerPluginInstallEvent::class => [ComposerPluginInstallListener::class],
        ComposerPluginUninstallEvent::class => [],
        ComposerPluginUpdateEvent::class => [ComposerPluginUpdateListener::class],
    ];

    /**
     * @param ListenerProvider $service
     *
     * @throws Throwable
     */
    #[Override]
    public function __invoke(ContainerInterface $container, object $service): void
    {
        assert($service instanceof ListenerProvider);

        foreach (self::EVENTS as $event => $listeners) {
            foreach ($listeners as $listener) {
                $service->listen($event, $listener);
            }
        }
    }
}
