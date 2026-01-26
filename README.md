# Handrail

[![GitHub Sponsors](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/handrail&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)
[![Automation](https://github.com/ghostwriter/handrail/actions/workflows/automation.yml/badge.svg)](https://github.com/ghostwriter/handrail/actions/workflows/automation.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/handrail?color=8892bf)](https://www.php.net/supported-versions)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/handrail?color=blue)](https://packagist.org/packages/ghostwriter/handrail)

Safeguard your PHP code by wrapping function declarations in `if (!function_exists())` blocks.

Ensures that functions are only declared if they do not already exist, preventing redeclaration conflicts.

### Star ⭐️ this repo if you find it useful

You can also star (🌟) this repo to find it easier later.

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/handrail
```

### Example

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

### Configuration

To configure the paths or files to scan, create a composer `extra` configuration in your `composer.json`:

```json
{
    "extra": {
        "ghostwriter/handrail": {
            "disable": false,
            "packages": [
                "vendor/package"
            ],
            "files": [
                "vendor/amphp/amp/src/functions.php",
                "relative/path/to/file.php"
            ]
        }
    }
}
```

- **`disable`**: (default: `false`) A boolean flag to enable or disable Handrail.
- **`files`**: (default: `[]`) An array of files to scan for function declarations.
- **`packages`**: (default: `[]`) An array of Composer packages to scan for function declarations.

## Usage

### Automatic Execution

After installing and configuring Handrail, we will automatically hook into Composer’s lifecycle events (`post-install-cmd` and `post-update-cmd`) after Composer installs or updates packages.

```bash
composer install
```

```bash
composer update
```

### Manual Execution

You can also run Handrail manually using the following Composer command:

```bash
composer handrail
```

## Advanced Usage

### Running Handrail Programmatically

Handrail provides an API for programmatic execution within PHP scripts:

```php
use Ghostwriter\Handrail\Handrail;

Handrail::new()->guard($phpFile);
```


### Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/handrail/contributors)

### Thanks

- [Yevhen Sidelnyk](https://github.com/rela589n) for the [inspiration](https://github.com/rela589n/knowledge-base/blob/a72b3071b770253dc61d03d8d2849e47a8229bc7/PHP/Psalm%20in%20a%20separate%20composer.json.md).

### Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information on what has changed recently.

### License

Please see [LICENSE](./LICENSE) for more information on the license that applies to this project.

### Security

Please see [SECURITY.md](./SECURITY.md) for more information on security disclosure process.
