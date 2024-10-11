<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Container;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\ServiceProviderInterface;
use Ghostwriter\EventDispatcher\EventDispatcher;
use Ghostwriter\EventDispatcher\Interface\EventDispatcherInterface;
use Ghostwriter\EventDispatcher\Interface\ListenerProviderInterface;
use Ghostwriter\EventDispatcher\ListenerProvider;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\Container\Factory\ListenerProviderFactory;
use Ghostwriter\Handrail\Handrail;
use Ghostwriter\Handrail\HandrailInterface;
use Ghostwriter\Json\Interface\JsonInterface;
use Ghostwriter\Json\Json;
use Override;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers a service on the given container.
     */
    #[Override]
    public function __invoke(ContainerInterface $container): void
    {
        $container->alias(ArgvInput::class, InputInterface::class);
        $container->alias(ConsoleOutput::class, OutputInterface::class);
        $container->alias(EventDispatcher::class, EventDispatcherInterface::class);
        $container->alias(Filesystem::class, FilesystemInterface::class);
        $container->alias(Handrail::class, HandrailInterface::class);
        $container->alias(ListenerProvider::class, ListenerProviderInterface::class);
        $container->alias(Json::class, JsonInterface::class);
        $container->factory(ListenerProvider::class, ListenerProviderFactory::class);
    }
}
