<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Ghostwriter\Container\Container;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\EventDispatcher\Interface\EventDispatcherInterface;
use Ghostwriter\Handrail\Console\Command\HandrailCommand;
use Ghostwriter\Handrail\Container\ServiceProvider;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginActivate;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginDeactivate;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPluginUninstall;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostInstall;
use Ghostwriter\Handrail\EventDispatcher\Event\ComposerPostUpdate;
use Override;
use Throwable;

final readonly class Plugin implements Capable, CommandProvider, EventSubscriberInterface, PluginInterface
{
    private ContainerInterface $container;

    private EventDispatcherInterface $eventDispatcher;

    private HandrailInterface $handrail;

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
        $this->handrail = $this->container->get(HandrailInterface::class);
    }

    /**
     * Apply plugin modifications to Composer.
     */
    #[Override]
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->eventDispatcher->dispatch(new ComposerPluginActivate($composer, $io));
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
        $this->eventDispatcher->dispatch(new ComposerPluginDeactivate($composer, $io));
    }

    #[Override]
    public function getCapabilities()
    {
        return [
            CommandProvider::class => self::class,
        ];
    }

    #[Override]
    public function getCommands()
    {
        return [$this->container->get(HandrailCommand::class)];
    }

    public function postInstall(\Composer\Script\Event $event): void
    {
        $this->eventDispatcher->dispatch(new ComposerPostInstall($event));
    }

    public function postUpdate(\Composer\Script\Event $event): void
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
        $this->eventDispatcher->dispatch(new ComposerPluginUninstall($composer, $io));
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array<string, array<array{0: string, 1?: int}>|array{0: string, 1?: int}|string> The event names to listen to
     */
    #[Override]
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'postInstall',
            //            ScriptEvents::POST_INSTALL_CMD => [fn()=>Handrail::postInstall()],
            ScriptEvents::POST_UPDATE_CMD => 'postUpdate',
            //            ScriptEvents::POST_UPDATE_CMD => [fn()=>Handrail::postUpdate()],
        ];
    }
}
