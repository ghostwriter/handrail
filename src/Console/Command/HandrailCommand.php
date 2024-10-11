<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Console\Command;

use Composer\Command\BaseCommand;
use Composer\InstalledVersions;
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

final class HandrailCommand extends BaseCommand
{
    public const array DEFAULT_COMPOSER_EXTRA = [
        Handrail::EXTRA => [
            Handrail::PACKAGE_NAME => [
                Handrail::OPTION_DISABLE => false,
                Handrail::OPTION_FILES => [],
            ],
        ],
    ];

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
        $this->inputOutput->title(
            \sprintf(
                'Handrail (%s) is safeguarding PHP functions from redeclaration conflicts.',
                InstalledVersions::getPrettyVersion(Handrail::PACKAGE_NAME),
            )
        );

        $extra = $this->requireComposer()
            ->getPackage()
            ->getExtra();

        if (! \array_key_exists(Handrail::PACKAGE_NAME, $extra)) {
            $this->inputOutput->error(
                \sprintf(
                    'Handrail is not configured, add the following configuration to your composer.json file.%s',
                    PHP_EOL . $this->json->encode(self::DEFAULT_COMPOSER_EXTRA, true),
                )
            );

            return 1;
        }

        /** @var array{disable: ?bool, files: ?list<string>} $config */
        $config = $extra[Handrail::PACKAGE_NAME];

        $disable = $config[Handrail::OPTION_DISABLE] ?? false;

        if (! \is_bool($disable)) {
            $this->inputOutput->error(\sprintf(
                'Invalid `disable` configuration; expected an "bool" but "%s" provided.%s',
                \get_debug_type($disable),
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

        if (! \is_array($files)) {
            $this->inputOutput->error(\sprintf(
                'Invalid `files` configuration; expected an "array" but "%s" provided.%s',
                \get_debug_type($files),
                PHP_EOL . $this->json->encode($config, true),
            ));

            return 1;
        }

        if ($files === []) {
            $this->inputOutput->success('No files to process.');

            return 0;
        }

        $workspace = $this->filesystem->currentWorkingDirectory();

        foreach ($files as $file) {
            if (! \is_string($file)) {
                $this->inputOutput->error(
                    \sprintf(
                        'Invalid file path; expected a "string" but "%s" provided.%s',
                        \get_debug_type($file),
                        PHP_EOL . $this->json->encode($files, true),
                    )
                );

                return 1;
            }

            $fullPath = $workspace . DIRECTORY_SEPARATOR . $file;

            if (! \str_ends_with($fullPath, '.php')) {
                $this->inputOutput->warning('Invalid PHP file: ' . $fullPath);

                continue;
            }

            $this->inputOutput->success('Processing: ' . $file);

            if (! \str_contains($fullPath, \sprintf('%svendor%s', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR))) {
                $this->inputOutput->error('Invalid vendor directory: ' . $fullPath);

                continue;
            }

            $this->handrail->guard($fullPath);
        }

        return 0;
    }
}
