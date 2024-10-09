<?php

declare(strict_types=1);

namespace Tests\Unit;

use Generator;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Handrail\Container\Factory\ListenerProviderFactory;
use Ghostwriter\Handrail\Container\ServiceProvider;
use Ghostwriter\Handrail\ExceptionInterface;
use Ghostwriter\Handrail\File\IncludedFile;
use Ghostwriter\Handrail\File\ModifiedFile;
use Ghostwriter\Handrail\Handrail;
use Ghostwriter\Handrail\HandrailInterface;
use Ghostwriter\Handrail\Modifier\FileModifier;
use Ghostwriter\Handrail\Path;
use Ghostwriter\Handrail\Paths\ExcludePaths;
use Ghostwriter\Handrail\Paths\IncludePaths;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use RuntimeException;
use SplFileInfo;
use Throwable;

use const DIRECTORY_SEPARATOR;

#[CoversClass(Handrail::class)]
#[UsesClass(ServiceProvider::class)]
#[UsesClass(ListenerProviderFactory::class)]
#[UsesClass(ExcludePaths::class)]
#[UsesClass(Filesystem::class)]
#[UsesClass(Path::class)]
#[UsesClass(FileModifier::class)]
#[UsesClass(ModifiedFile::class)]
#[UsesClass(IncludedFile::class)]
#[UsesClass(IncludePaths::class)]
final class HandrailTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    #[DataProvider('provideGuardCases')]
    public function testGuard(string $code, string $expected): void
    {
        $temporaryFile = self::$filesystem->createTemporaryFile();

        self::$filesystem->write($temporaryFile, $code);

        // Act
        Handrail::new()->guard($temporaryFile);

        // Assert
        self::assertStringEqualsFile($temporaryFile, $expected);
    }

    /**
     * @throws Throwable
     */
    public function testImplementsInterface(): void
    {
        self::assertTrue(\is_a(Handrail::class, HandrailInterface::class, true));
        self::assertTrue(\is_a(ExceptionInterface::class, Throwable::class, true));
    }

    /**
     * @throws Throwable
     *
     * @return Generator<string,array{string,string}>
     */
    public static function provideGuardCases(): Generator
    {
        yield from [
            'empty' => ['', ''],
        ];

        $filesystem = self::filesystem();

        foreach ($filesystem->listDirectory(self::fixturesDirectory()) as $splFileInfo) {
            if (! $splFileInfo instanceof SplFileInfo) {
                throw new RuntimeException('Expected SplFileInfo');
            }

            if (! $splFileInfo->isDir()) {
                continue;
            }

            $path = $splFileInfo->getPathname();

            $fixture = $path . DIRECTORY_SEPARATOR . 'fixture.php';
            $expected = $path . DIRECTORY_SEPARATOR . 'expected.php';

            yield from [
                $splFileInfo->getBasename() => [$filesystem->read($fixture), $filesystem->read($expected)],
            ];
        }
    }
}
