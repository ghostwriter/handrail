<?php

declare(strict_types=1);

namespace Ghostwriter\Handrail\Exception;

use Ghostwriter\Handrail\ExceptionInterface;
use RuntimeException;

final class ShouldNotHappenException extends RuntimeException implements ExceptionInterface
{
}
