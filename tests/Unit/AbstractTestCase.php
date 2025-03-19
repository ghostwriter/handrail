<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Override;
use PHPUnit\Framework\TestCase;
use Throwable;

use const DIRECTORY_SEPARATOR;

use function mb_strrchr;
use function mb_substr;

abstract class AbstractTestCase extends TestCase
{
    protected static ?FilesystemInterface $filesystem;

    protected static ?string $temporaryDirectory;

    /**
     * @throws Throwable
     */
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::$filesystem = self::filesystem();

        self::$temporaryDirectory = self::temporaryDirectory();
    }

    #[Override]
    protected function tearDown(): void
    {
        self::$filesystem->delete(self::$temporaryDirectory);

        parent::tearDown();
    }

    public static function filesystem(): FilesystemInterface
    {
        return self::$filesystem ??= Filesystem::new();
    }

    /**
     * @throws Throwable
     */
    public static function fixturesDirectory(): string
    {
        return self::filesystem()
            ->parentDirectory(__DIR__) . DIRECTORY_SEPARATOR . 'Fixture';
    }

    /**
     * @throws Throwable
     */
    public static function temporaryDirectory(): string
    {
        return self::filesystem()
            ->createTemporaryDirectory(mb_substr(mb_strrchr(static::class, '\\'), 1));
    }
}
