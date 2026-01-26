<?php

declare(strict_types=1);

use function function_exists;

final class ExampleClass {
    public function __construct(
        private string $name,
        private int $age,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}

if (!function_exists('exampleFunction')) {
    function exampleFunction() {
        // some code
    }
}

if (!function_exists('anotherFunction')) {
    function anotherFunction() {
        // more code
    }
}
