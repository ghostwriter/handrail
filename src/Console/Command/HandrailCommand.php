<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Console\Command;

use Composer\Command\BaseCommand;
use Composer\InstalledVersions;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Handrail\Console\InputOutput;
use Ghostwriter\Handrail\HandrailInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use const DIRECTORY_SEPARATOR;

final class HandrailCommand extends BaseCommand
{
    public function __construct(
        private HandrailInterface $handrail,
        private InputOutput $inputOutput,
        private FilesystemInterface $filesystem
    ) {
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    protected function configure(): void
    {
        $this
            ->setName('handrail')
            ->setDescription('Safeguard PHP functions from redeclaration conflicts.');
    }

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->inputOutput->title(
            \sprintf(
                'Handrail (%s) is safeguarding PHP functions from redeclaration conflicts.',
                InstalledVersions::getPrettyVersion('ghostwriter/handrail'),
            )
        );

        $filesystem = $this->filesystem;

        $workspace = $filesystem->currentWorkingDirectory();

        $composerJson = $workspace . DIRECTORY_SEPARATOR . 'composer.json';

        if (! $filesystem->isFile($composerJson)) {
            $this->inputOutput->error('Invalid composer.json: ' . $composerJson);
            return 1;
        }

        $composerJson = \json_decode($filesystem->read($composerJson), true);

        $disable = $composerJson['extra']['ghostwriter/handrail']['disable'] ?? false;

        if (! \is_bool($disable)) {
            $this->inputOutput->error('Invalid disable value: ' . \get_debug_type($disable));
            return 1;
        }

        if ($disable) {
            $this->inputOutput->warning('Handrail is disabled.');
            return 0;
        }

        $include = $composerJson['extra']['ghostwriter/handrail']['include'] ?? [];

        if (! \is_array($include)) {
            $this->inputOutput->error('Invalid include value: ' . \get_debug_type($include));
            return 1;
        }

        $exclude = $composerJson['extra']['ghostwriter/handrail']['exclude'] ?? [];

        if (! \is_array($exclude)) {
            $this->inputOutput->error('Invalid exclude value: ' . \get_debug_type($exclude));
            return 1;
        }

        if ($include === [] && $exclude === []) {
            $this->inputOutput->success('No include or exclude paths are defined.');

            return 0;
        }

        foreach ($include as $path) {
            if (! \is_string($path)) {
                $this->inputOutput->error('Invalid include path: ' . \get_debug_type($path));
                return 1;
            }

            $this->inputOutput->success('Process: ' . $path);

            $fullPath = $filesystem->parentDirectory($workspace . DIRECTORY_SEPARATOR . $path);

            foreach ($this->inputOutput->iterate($filesystem->recursiveIterator($fullPath)) as $file) {
                $path = $file->toString();

                if (! \str_ends_with($path, '.php')) {
                    $this->inputOutput->warning('Excluded non-PHP: ' . $path);
                    continue;
                }

                if (\str_starts_with($path, $workspace . DIRECTORY_SEPARATOR . 'src')) {
                    $this->inputOutput->warning('Excluded src: ' . $path);
                    continue;
                }

                if (\str_starts_with($path, $workspace . DIRECTORY_SEPARATOR . 'tests')) {
                    $this->inputOutput->warning('Excluded tests: ' . $path);
                    continue;
                }

                foreach ($exclude as $excludePath) {
                    if (\str_contains($path, $excludePath)) {
                        $this->inputOutput->warning('Excluded: ' . $path);
                        continue 2;
                    }
                }
                //                if (! \str_contains($path, \sprintf('%svendor%s', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR))) {
                //                    $this->inputOutput->error('Invalid vendor directory: ' . $path);
                //                    continue;
                //                }

                if (! \str_contains($path, \sprintf('%svendor%s', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR))) {
                    $this->inputOutput->error('Invalid vendor directory: ' . $path);

                    continue;
                }

                $this->inputOutput->success('Processing: ' . $path);

                $this->handrail->include($path);
            }

            $this->handrail->guard($fullPath);
        }

        return 0;
    }
}
