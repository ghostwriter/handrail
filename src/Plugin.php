<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

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
use Ghostwriter\Handrail\Container\ServiceProvider;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostInstall;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostUpdate;
use Override;
use Throwable;

final readonly class Plugin implements Capable, CommandProvider, EventSubscriberInterface, PluginInterface
{
    public const array CAPABILITIES = [
        CommandProvider::class => self::class,
    ];

    private ContainerInterface $container;

    private EventDispatcherInterface $eventDispatcher;

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $container = Container::getInstance();

        if (! $container->has(ServiceProvider::class)) {
            $container->provide(ServiceProvider::class);
        }

        $this->container = $container;
        $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    /**
     * Apply plugin modifications to Composer.
     */
    #[Override]
    public function activate(Composer $composer, IOInterface $io): void
    {
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
    }

    #[Override]
    public function getCapabilities()
    {
        return self::CAPABILITIES;
    }

    #[Override]
    public function getCommands()
    {
        static $commands;

        return $commands ??= [$this->container->get(HandrailCommand::class)];
    }

    public function postInstall(Event $event): void
    {
        $this->eventDispatcher->dispatch(new ComposerPostInstall($event));
    }

    public function postUpdate(Event $event): void
    {
        $this->eventDispatcher->dispatch(new ComposerPostUpdate($event));
    }

    /**
     * Prepare the plugin to be uninstalled.
     *
     * This will be called after deactivate.
     */
    #[Override]
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * @return array<string,string> The event names to listen to
     */
    #[Override]
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'postInstall',
            ScriptEvents::POST_UPDATE_CMD => 'postUpdate',
        ];
    }
}
