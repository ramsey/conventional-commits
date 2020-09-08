<h1 align="center">ramsey/conventional-commits</h1>

<p align="center">
    <strong>A PHP library for creating and validating commit messages.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/conventional-commits"><img src="http://img.shields.io/badge/source-ramsey/conventional--commits-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/conventional-commits"><img src="https://img.shields.io/packagist/v/ramsey/conventional-commits.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/conventional-commits.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/conventional-commits/actions?query=workflow%3ACI"><img src="https://img.shields.io/github/workflow/status/ramsey/conventional-commits/CI?logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/conventional-commits"><img src="https://img.shields.io/codecov/c/gh/ramsey/conventional-commits?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/conventional-commits"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fconventional-commits%2Fcoverage" alt="Psalm Type Coverage"></a>
    <a href="https://github.com/ramsey/conventional-commits/blob/master/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/conventional-commits.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://packagist.org/packages/ramsey/conventional-commits/stats"><img src="https://img.shields.io/packagist/dt/ramsey/conventional-commits.svg?style=flat-square&colorB=darkmagenta" alt="Package downloads on Packagist"></a>
    <a href="https://phpc.chat/channel/ramsey"><img src="https://img.shields.io/badge/phpc.chat-%23ramsey-darkslateblue?style=flat-square" alt="Chat with the maintainers"></a>
</p>

## About

ramsey/conventional-commits is a PHP library for creating and validating commit
messages according to the [Conventional Commits specification][]. It also
includes a [CaptainHook][] plugin!

This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.

## Installation

Install this package as a development dependency using
[Composer](https://getcomposer.org).

``` bash
composer require --dev ramsey/conventional-commits
```

## Usage

To use the `conventional-commits` console command to help you prepare commit
messages according to Conventional Commits, enter the following in your console:

``` bash
./vendor/bin/conventional-commits prepare
```

To see all the features of the console command, enter:

``` bash
./vendor/bin/conventional-commits
```

### Validating Commit Messages

To use the CaptainHook plugin to validate commit messages according to the
Conventional Commits specification, add the following to the `commit-msg`
property in your `captainhook.json` file:

``` json
{
    "commit-msg": {
        "enabled": true,
        "actions": [
            {
                "action": "\\Ramsey\\CaptainHook\\ValidateConventionalCommit"
            }
        ]
    }
}
```

### Preparing Commit Messages

You can set up this library to prompt you to prepare commit messages when you
use `git commit`!

To use the CaptainHook plugin to prepare commit messages according to the
Conventional Commits specification, add the following to the `prepare-commit-msg`
property in your `captainhook.json` file:

``` json
{
    "prepare-commit-msg": {
        "enabled": true,
        "actions": [
            {
                "action": "\\Ramsey\\CaptainHook\\PrepareConventionalCommit"
            }
        ]
    }
}
```

## Contributing

Contributions are welcome! Before contributing to this project, familiarize
yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

To develop this project, you will need [PHP](https://www.php.net) 7.4 or greater
and [Composer](https://getcomposer.org).

After cloning this repository locally, execute the following commands:

``` bash
cd /path/to/conventional-commits
composer install
```

Now, you are ready to develop!

### Tooling

This project uses [CaptainHook](https://github.com/CaptainHookPhp/captainhook)
to validate all staged changes prior to commit.

#### Composer Commands

To see all the commands available in the project `dev` namespace for
Composer, type:

``` bash
composer list dev
```

#### Coding Standards

This project follows a superset of [PSR-12](https://www.php-fig.org/psr/psr-12/)
coding standards, enforced by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).
The project PHP_CodeSniffer configuration may be found in `phpcs.xml.dist`.

CaptainHook will run PHP_CodeSniffer before committing. It will attempt to fix
any errors it can, and it will reject the commit if there are any un-fixable
issues. Many issues can be fixed automatically and will be done so pre-commit.

You may lint the entire codebase using PHP_CodeSniffer with the following
commands:

``` bash
# Lint
composer dev:lint

# Lint and autofix
composer dev:lint:fix
```

#### Static Analysis

This project uses a combination of [PHPStan](https://github.com/phpstan/phpstan)
and [Psalm](https://github.com/vimeo/psalm) to provide static analysis of PHP
code. Configurations for these are in `phpstan.neon.dist` and `psalm.xml`,
respectively.

CaptainHook will run PHPStan and Psalm before committing. The pre-commit hook
does not attempt to fix any static analysis errors. Instead, the commit will
fail, and you must fix the errors manually.

You may run static analysis manually across the whole codebase with the
following command:

``` bash
# Static analysis
composer dev:analyze
```

#### Project Structure

This project uses [pds/skeleton](https://github.com/php-pds/skeleton) as its
base folder structure and layout.

## Copyright and License

The ramsey/conventional-commits library is copyright © [Ben Ramsey](https://benramsey.com)
and licensed for use under the terms of the MIT License (MIT). Please see
[LICENSE](LICENSE) for more information.


[Conventional Commits specification]: https://www.conventionalcommits.org/en/v1.0.0/
[CaptainHook]: https://github.com/captainhookphp/captainhook
