# Handrail

[![Automation](https://github.com/ghostwriter/handrail/actions/workflows/automation.yml/badge.svg)](https://github.com/ghostwriter/handrail/actions/workflows/automation.yml)
[![PHP Version](https://badgen.net/packagist/php/ghostwriter/handrail?color=777BB4)](https://www.php.net/supported-versions)
[![Packagist Downloads](https://badgen.net/packagist/dt/ghostwriter/handrail?color=F28D1A)](https://packagist.org/packages/ghostwriter/handrail)
[![PayPal](https://img.shields.io/badge/paypal-@codepoet-0079C1?logo=data%3Aimage%2Fsvg%2Bxml%3Bbase64%2CPHN2ZyB2aWV3Qm94PSIwIDAgMjQgMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI%2BPHBhdGggZD0iTTE5LjcxNSA2LjEzM2MuMjQ5LTEuODY2IDAtMy4xMS0uOTk5LTQuMjY2QzE3LjYzNC42MjIgMTUuNzIxIDAgMTMuMzA3IDBINi4yMzVjLS40MTggMC0uOTE2LjQ0NC0xIC44ODlMMi4zMjMgMjAuNjIyYzAgLjM1Ni4yNS44LjY2NS44aDQuMzI4bC0uMjUgMS45NTZjLS4wODQuMzU1LjE2Ni42MjIuNDk4LjYyMmgzLjY2M2MuNDE3IDAgLjgzMi0uMjY3LjkxNS0uNzExdi0uMjY3bC43NDktNC42MjJ2LS4xNzhjLjA4My0uNDQ0LjUtLjguOTE1LS44aC41YzMuNTc4IDAgNi4zMjUtMS41MSA3LjE1Ni01Ljk1NS40MTgtMS44NjcuMjUyLTMuMzc4LS43NDctNC40NDUtLjI1LS4zNTUtLjY2Ni0uNjIyLTEtLjg4OSIgZmlsbD0iIzAwOWNkZSIvPjxwYXRoIGQ9Ik0xOS43MTUgNi4xMzNjLjI0OS0xLjg2NiAwLTMuMTEtLjk5OS00LjI2NkMxNy42MzQuNjIyIDE1LjcyMSAwIDEzLjMwNyAwSDYuMjM1Yy0uNDE4IDAtLjkxNi40NDQtMSAuODg5TDIuMzIzIDIwLjYyMmMwIC4zNTYuMjUuOC42NjUuOGg0LjMyOGwxLjE2NC03LjM3OC0uMDgzLjI2N2MuMDg0LS41MzMuNS0uODg5Ljk5OC0uODg5aDIuMDhjNC4wNzkgMCA3LjI0MS0xLjc3OCA4LjI0LTYuNzU1LS4wODMtLjI2NyAwLS4zNTYgMC0uNTM0IiBmaWxsPSIjMDEyMTY5Ii8%2BPHBhdGggZD0iTTkuNTYzIDYuMTMzYy4wODItLjI2Ni4yNS0uNTMzLjQ5OC0uNzEuMTY2IDAgLjI1LS4wOS40MTYtLjA5aDUuNDk0Yy42NjYgMCAxLjMzLjA5IDEuODMuMTc4LjE2NiAwIC4zMzMgMCAuNDk4LjA4OS4xNjguMDg5LjMzNC4wODkuNDE4LjE3OGguMjVjLjI0OC4wODkuNDk3LjI2Ni43NDguMzU1LjI0OC0xLjg2NiAwLTMuMTEtLjk5OS00LjM1NUMxNy43MTcuNTMzIDE1LjgwNCAwIDEzLjM5IDBINi4yMzVjLS40MTggMC0uOTE2LjM1Ni0xIC44ODlMMi4zMjMgMjAuNjIyYzAgLjM1Ni4yNS44LjY2NS44aDQuMzI4bDEuMTY0LTcuMzc4IDEuMDg0LTcuOTF6IiBmaWxsPSIjMDAzMDg3Ii8%2BPC9zdmc%2B)](https://paypal.me/codepoet)
[![Sponsors via GitHub](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/handrail&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)

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
