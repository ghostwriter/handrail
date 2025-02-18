<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Console\Command;

use Composer\Command\BaseCommand;
use Composer\InstalledVersions;
use Composer\Package\PackageInterface;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\Console\InputOutput;
use Ghostwriter\Handrail\Handrail;
use Ghostwriter\Handrail\HandrailInterface;
use Ghostwriter\Json\Interface\JsonInterface;
use Override;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

use function array_key_exists;
use function get_debug_type;
use function is_array;
use function is_bool;
use function is_string;
use function sprintf;
use function str_contains;
use function str_ends_with;

final class HandrailCommand extends BaseCommand
{
    public const array DEFAULT_COMPOSER_EXTRA = [
        Handrail::EXTRA => [
            Handrail::PACKAGE_NAME => [
                Handrail::OPTION_DISABLE => false,
                Handrail::OPTION_FILES => [],
                Handrail::OPTION_PACKAGES => [],
            ],
        ],
    ];

    /**
     * @throws Throwable
     */
    public function __construct(
        private readonly HandrailInterface $handrail,
        private readonly InputOutput $inputOutput,
        private readonly FilesystemInterface $filesystem,
        private readonly JsonInterface $json
    ) {
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    #[Override]
    protected function configure(): void
    {
        $this
            ->setName('handrail')
            ->setDescription('Safeguard PHP functions from redeclaration conflicts.');
    }

    /**
     * @throws Throwable
     */
    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! InstalledVersions::isInstalled(Handrail::PACKAGE_NAME)) {
            $this->inputOutput->error(sprintf('Handrail (%s) is not installed.', Handrail::PACKAGE_NAME));

            return 0;
        }

        $this->inputOutput->title(
            sprintf(
                'Handrail (%s) is safeguarding PHP functions from redeclaration conflicts.',
                InstalledVersions::getPrettyVersion(Handrail::PACKAGE_NAME),
            )
        );

        $composer = $this->requireComposer();

        $rootPackage = $composer->getPackage();

        $extra = $rootPackage->getExtra();

        if (! array_key_exists(Handrail::PACKAGE_NAME, $extra)) {
            /** @var array{ghostwriter/handrail: array{disable: bool, files: list<string>, packages: list<string>}} $extra */
            $extra = self::DEFAULT_COMPOSER_EXTRA[Handrail::EXTRA];
        }

        /** @var array{disable: ?bool, files: ?list<string>, packages: ?list<string>} $config */
        $config = $extra[Handrail::PACKAGE_NAME];

        $disable = $config[Handrail::OPTION_DISABLE] ?? false;

        if (! is_bool($disable)) {
            $this->inputOutput->error(sprintf(
                'Invalid `disable` configuration; expected an "bool" but "%s" provided.%s',
                get_debug_type($disable),
                PHP_EOL . $this->json->encode($config, true),
            ));

            return 1;
        }

        if ($disable) {
            $this->inputOutput->warning('Handrail is disabled.');

            return 0;
        }

        /** @var ?list<?string> $files */
        $files = $config[Handrail::OPTION_FILES] ?? [];

        if (! is_array($files)) {
            $this->inputOutput->error(sprintf(
                'Invalid `files` configuration; expected an "array" but "%s" provided.%s',
                get_debug_type($files),
                PHP_EOL . $this->json->encode($config, true),
            ));

            return 1;
        }

        /** @var ?list<?string> $packages */
        $packages = $config[Handrail::OPTION_PACKAGES] ?? [];
        if (! is_array($packages)) {
            $this->inputOutput->error(sprintf(
                'Invalid `packages` configuration; expected an "array" but "%s" provided.%s',
                get_debug_type($packages),
                PHP_EOL . $this->json->encode($config, true),
            ));

            return 1;
        }

        $installedRepository = $composer
            ->getRepositoryManager()
            ->getLocalRepository();

        foreach ($packages as $name) {
            $package = $installedRepository->findPackage($name, '*');
            if (! $package instanceof PackageInterface) {
                $this->inputOutput->warning('Package not installed: ' . $name);

                continue;
            }

            $this->inputOutput->info('Processing package: ' . $name);

            foreach ($package->getAutoload()['files'] ?? [] as $filePath) {
                $files[] = sprintf('vendor%s%s%s%s', DIRECTORY_SEPARATOR, $name, DIRECTORY_SEPARATOR, $filePath);
            }
        }

        if ([] === $files) {
            $this->inputOutput->success('No files to process.');

            return 0;
        }

        $workspace = $this->filesystem->currentWorkingDirectory() . DIRECTORY_SEPARATOR;

        foreach ($files as $file) {
            if (! is_string($file)) {
                $this->inputOutput->error(
                    sprintf(
                        'Invalid file path; expected a "string" but "%s" provided.%s',
                        get_debug_type($file),
                        PHP_EOL . $this->json->encode($files, true),
                    )
                );

                return 1;
            }

            $fullPath = $workspace . $file;

            if (! str_ends_with($fullPath, '.php')) {
                $this->inputOutput->warning('Invalid PHP file: ' . $fullPath);

                continue;
            }

            $this->inputOutput->info('Processing: ' . $file);

            if (! str_contains($fullPath, sprintf('%svendor%s', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR))) {
                $this->inputOutput->error('Invalid vendor directory: ' . $fullPath);

                continue;
            }

            try {
                $this->handrail->guard($fullPath);

                $this->inputOutput->success('Processed: ' . $file);
            } catch (Throwable $exception) {
                $this->inputOutput->catch($exception);
            }

        }

        return 0;
    }
}
