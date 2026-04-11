<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Ghostwriter\Container\Container;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\EventDispatcher\Interface\EventDispatcherInterface;
use Ghostwriter\Handrail\Console\Command\HandrailCommand;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginActivateEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginDeactivateEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginInstallEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginUninstallEvent;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginUpdateEvent;
use Override;
use Throwable;

final readonly class Plugin implements Capable, CommandProvider, EventSubscriberInterface, PluginInterface
{
    private const array CAPABILITIES = [
        CommandProvider::class => self::class,
    ];

    private ContainerInterface $container;

    private EventDispatcherInterface $eventDispatcher;

    /** @throws Throwable */
    public function __construct()
    {
        $this->container = Container::getInstance();

        $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    /** Apply plugin modifications to Composer. */
    #[Override]
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->eventDispatcher->dispatch(new ComposerPluginActivateEvent($composer, $io));
    }

    /**
     * Remove any hooks from Composer.
     *
     * This will be called when a plugin is deactivated before being uninstalled,
     * but also before it gets upgraded to a new version,
     * so the old one can be deactivated and the new one activated.
     */
    #[Override]
    public function deactivate(Composer $composer, IOInterface $io): void
    {
        $this->eventDispatcher->dispatch(new ComposerPluginDeactivateEvent($composer, $io));
    }

    #[Override]
    public function getCapabilities()
    {
        return self::CAPABILITIES;
    }

    /**
     * Retrieves an array of commands.
     *
     * @throws Throwable
     *
     * @return BaseCommand[]
     */
    #[Override]
    public function getCommands()
    {
        return [$this->container->get(HandrailCommand::class)];
    }

    /** @throws Throwable */
    public function postInstall(Event $event): void
    {
        $this->eventDispatcher->dispatch(new ComposerPluginInstallEvent($event));
    }

    /** @throws Throwable */
    public function postUpdate(Event $event): void
    {
        $this->eventDispatcher->dispatch(new ComposerPluginUpdateEvent($event));
    }

    /**
     * Prepare the plugin to be uninstalled.
     *
     * This will be called after deactivate.
     */
    #[Override]
    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $this->eventDispatcher->dispatch(new ComposerPluginUninstallEvent($composer, $io));
    }

    /** @return array<string,string> The event names to listen to */
    #[Override]
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'postInstall',
            ScriptEvents::POST_UPDATE_CMD => 'postUpdate',
        ];
    }
}
