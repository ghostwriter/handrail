<?php

declare(strict_types=1);

namespace Tests\Unit;

use Generator;
use Ghostwriter\Handrail\Container\Factory\ListenerProviderFactory;
use Ghostwriter\Handrail\Container\ServiceProvider;
use Ghostwriter\Handrail\Exception\ShouldNotHappenException;
use Ghostwriter\Handrail\Handrail;
use Ghostwriter\Handrail\HandrailInterface;
use Ghostwriter\Handrail\Modifier\FunctionDeclarationModifier;
use Ghostwriter\Handrail\Value\ExceptionInterface;
use Ghostwriter\Handrail\Value\File\ModifiedFile;
use Ghostwriter\Handrail\Value\File\OriginalFile;
use Ghostwriter\Handrail\Value\Path;
use Ghostwriter\Handrail\Value\Paths;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SplFileInfo;
use Throwable;

use const DIRECTORY_SEPARATOR;

use function is_a;

#[CoversClass(FunctionDeclarationModifier::class)]
#[CoversClass(Handrail::class)]
#[CoversClass(ListenerProviderFactory::class)]
#[CoversClass(ModifiedFile::class)]
#[CoversClass(OriginalFile::class)]
#[CoversClass(Path::class)]
#[CoversClass(Paths::class)]
#[CoversClass(ServiceProvider::class)]
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
        self::assertTrue(is_a(Handrail::class, HandrailInterface::class, true));
        self::assertTrue(is_a(ExceptionInterface::class, Throwable::class, true));
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
                throw new ShouldNotHappenException('Expected SplFileInfo');
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
