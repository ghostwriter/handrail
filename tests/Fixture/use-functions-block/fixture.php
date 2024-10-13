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

function exampleFunction() {
    // some code
}

function anotherFunction() {
    // more code
}
