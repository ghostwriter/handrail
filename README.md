# Handrail

[![Automation](https://github.com/ghostwriter/handrail/actions/workflows/automation.yml/badge.svg)](https://github.com/ghostwriter/handrail/actions/workflows/automation.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/handrail?color=8892bf)](https://www.php.net/supported-versions)
[![GitHub Sponsors](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/handrail&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)
[![Code Coverage](https://codecov.io/gh/ghostwriter/handrail/branch/main/graph/badge.svg)](https://codecov.io/gh/ghostwriter/handrail)
[![Type Coverage](https://shepherd.dev/github/ghostwriter/handrail/coverage.svg)](https://shepherd.dev/github/ghostwriter/handrail)
[![Psalm Level](https://shepherd.dev/github/ghostwriter/handrail/level.svg)](https://psalm.dev/docs/running_psalm/error_levels)
[![Latest Version on Packagist](https://badgen.net/packagist/v/ghostwriter/handrail)](https://packagist.org/packages/ghostwriter/handrail)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/handrail?color=blue)](https://packagist.org/packages/ghostwriter/handrail)

> [!WARNING]
>
> This project is not finished yet, please do not use it in production.

Safeguard your PHP code by wrapping function declarations in `if (!function_exists())` blocks.

This ensures that functions are only declared if they do not already exist, preventing redeclaration conflicts.

### Star ⭐️ this repo if you find it useful

You can also star (🌟) this repo to find it easier later.

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/handrail
```

### Configuration

To configure the paths or files to scan, create a composer `extra` configuration in your `composer.json`:

```json
{
    "extra": {
        "ghostwriter/handrail": {
            "disable": true, # Optional - Default: false
            "include": [
                "tests/Fixture/Exclude/includeFile.php",
                "tests/Fixture/Include/"
            ],
            "exclude": [
                "tests/Fixture/Exclude/",
                "tests/Fixture/Include/excludeFile.php"
            ]
        }
    }
}
```

- **`disable`**: (default: `false`) A boolean flag to enable or disable Handrail.
- **`exclude`**: (default: `[]`) An array of directories or files to exclude from the scan.
- **`include`**: (default: `[]`) An array of directories or files to include in the scan.



## Usage

Handrail automatically hooks into Composer’s lifecycle events (`post-install-cmd` and `post-update-cmd`).

- **Once installed, it will scan all PHP files autoloaded via composer and wrap function declarations as necessary.**


## How It Works

1. **Tokenization**  
   Handrail tokenizes each PHP file using `PhpToken::tokenize()` to parse the code structure accurately and identify all function declarations.

2. **Function Existence Check**  
   It looks for existing `if (!function_exists())` checks before each function. If none is found, it wraps the function declaration automatically.

3. **File Modification**  
   The modified file is written back, ensuring your codebase stays safe from redeclaration conflicts.

### Example #1

Before running Handrail:

```php
<?php

function exampleFunction() {
    // some code
}

function anotherFunction() {
    // more code
}

```

After running Handrail:

```php
<?php

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

```

## Advanced Usage

### Running Handrail Programmatically

Handrail provides an API for programmatic execution within PHP scripts:

```php
use Ghostwriter\Handrail\Handrail;

$includePaths = [
    'tests/Fixture/Exclude/includeFile.php',
    '/path/to/directory/Include/',
];

$excludePaths = [  
    'vendor/Exclude/',
    'tests/Fixture/Exclude/',
    'tests/Fixture/Include/excludeFile.php',
];

Handrail::new($includePaths, $excludePaths)->guard($fileOrDirectory);
```

### Manual Execution

You can also run Handrail manually using the following Composer command:

```bash
composer handrail
```

### Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/handrail/contributors)

### Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information on what has changed recently.

### License

Please see [LICENSE](./LICENSE) for more information on the license that applies to this project.

### Security

Please see [SECURITY.md](./SECURITY.md) for more information on security disclosure process.
