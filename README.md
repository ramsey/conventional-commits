<h1 align="center">ramsey/conventional-commits</h1>

<p align="center">
    <strong>A PHP library for creating and validating commit messages.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/conventional-commits"><img src="http://img.shields.io/badge/source-ramsey/conventional--commits-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/conventional-commits"><img src="https://img.shields.io/packagist/v/ramsey/conventional-commits.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/conventional-commits.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/conventional-commits/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/conventional-commits.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ramsey/conventional-commits/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/workflow/status/ramsey/conventional-commits/build/main?style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/conventional-commits"><img src="https://img.shields.io/codecov/c/gh/ramsey/conventional-commits?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/conventional-commits"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fconventional-commits%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>

## About

ramsey/conventional-commits is a PHP library for creating and validating commit
messages according to the [Conventional Commits specification][]. It also
includes a [CaptainHook][] action!

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

You can also validate the commit message using the following command:

``` bash
./vendor/bin/conventional-commits validate "[commit message]"
```

If you don't provide a commit message in the command line, the command will
prompt you for it.

To see all the features of the console command, enter:

``` bash
./vendor/bin/conventional-commits
```

### CaptainHook Action

To use ramsey/conventional-commits with CaptainHook as part of your `commit-msg`
and/or `prepare-commit-msg` Git hooks, be sure to require CaptainHook as a
development dependency.

Check out the [CaptainHook documentation](https://github.com/captainhookphp/captainhook)
for more information on installing and configuring CaptainHook.

#### Validating Commit Messages

To use the CaptainHook action to validate commit messages according to the
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

#### Preparing Commit Messages

You can set up this library to prompt you to prepare commit messages when you
use `git commit`!

To use the CaptainHook action to prepare commit messages according to the
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

### Configuration

Configuring ramsey/conventional-commits offers control over a few more aspects
of commit messages, such as letter case (i.e. lower, upper), allowed types and
scopes, required footers, and more.

We look for configuration in one of two places:

* `composer.json`
* `captainhook.json`

> ⚠️ **Please note:** if your `composer.json` file is not in the same location as
> the `vendor/` directory, we might have trouble locating it. Feel free to open
> an issue, and we'll work with you to see if we can find a solution.

#### Configuration Properties

Configuration for ramsey/conventional-commits consists of the following
properties:

| Property             | Description                                                                                                                                                                                       |
|----------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `typeCase`           | The letter case to require for the type. By default, any letter case is acceptable.                                                                                                               |
| `types`              | An array of accepted types (in addition to "feat" and "fix"). By default, any type is acceptable.                                                                                                 |
| `scopeCase`          | The letter case to require for the scope. By default, any letter case is acceptable.                                                                                                              |
| `scopeRequired`      | Whether a scope is required. By default, scope is not required.                                                                                                                                   |
| `scopes`             | An array of accepted scopes. By default, any scope is acceptable.                                                                                                                                 |
| `descriptionCase`    | The letter case to require for the description. By default, any letter case is acceptable.                                                                                                        |
| `descriptionEndMark` | A character to require at the end of the description. By default, any character is allowed. Use an empty string to indicate a punctuation character is not allowed at the end of the description. |
| `bodyRequired`       | Whether a body is required. By default, a body is not required.                                                                                                                                   |
| `bodyWrapWidth`      | An integer indicating the character width to auto-wrap the body of the commit message. By default, the commit body does not auto-wrap.                                                            |
| `requiredFooters`    | An array of footers that must be provided. By default, footers are not required.                                                                                                                  |

When specifying configuration, if you wish to use the default behavior for a
property, it is not necessary to list the property in your configuration.

Recognized letter cases are:

| Identifier | Name          | Example               |
|------------|---------------|-----------------------|
| `ada`      | Ada case      | `The_Quick_Brown_Fox` |
| `camel`    | Camel case    | `theQuickBrownFox`    |
| `cobol`    | COBOL case    | `THE-QUICK-BROWN-FOX` |
| `dot`      | Dot notation  | `the.quick.brown.fox` |
| `kebab`    | Kebab case    | `the-quick-brown-fox` |
| `lower`    | Lower case    | `the quick brown fox` |
| `macro`    | Macro case    | `THE_QUICK_BROWN_FOX` |
| `pascal`   | Pascal case   | `TheQuickBrownFox`    |
| `sentence` | Sentence case | `The quick brown fox` |
| `snake`    | Snake case    | `the_quick_brown_fox` |
| `title`    | Title case    | `The Quick Brown Fox` |
| `train`    | Train case    | `The-Quick-Brown-Fox` |
| `upper`    | Upper case    | `THE QUICK BROWN FOX` |

#### Configuration in composer.json

If you choose to put your configuration in `composer.json`, place it within the
`extra` property, namespaced under `ramsey/conventional-commits`, like this:

``` json
{
    "extra": {
        "ramsey/conventional-commits": {
            "config": {
                "typeCase": null,
                "types": [],
                "scopeCase": null,
                "scopeRequired": false,
                "scopes": [],
                "descriptionCase": null,
                "descriptionEndMark": null,
                "bodyRequired": false,
                "bodyWrapWidth": null,
                "requiredFooters": []
            }
        }
    }
}
```

> 📝 The properties in this example represent the default values.

#### Configuration in captainhook.json

If you choose to put your configuration in `captainhook.json`, you must provide
it for *each action* you configure, like this:

``` json
{
    "commit-msg": {
        "enabled": true,
        "actions": [
            {
                "action": "\\Ramsey\\CaptainHook\\ValidateConventionalCommit",
                "options": {
                    "config": {
                        "typeCase": null,
                        "types": [],
                        "scopeCase": null,
                        "scopeRequired": false,
                        "scopes": [],
                        "descriptionCase": null,
                        "descriptionEndMark": null,
                        "bodyRequired": false,
                        "bodyWrapWidth": null,
                        "requiredFooters": []
                    }
                }
            }
        ]
    },
    "prepare-commit-msg": {
        "enabled": true,
        "actions": [
            {
                "action": "\\Ramsey\\CaptainHook\\PrepareConventionalCommit",
                "options": {
                    "config": {
                        "typeCase": null,
                        "types": [],
                        "scopeCase": null,
                        "scopeRequired": false,
                        "scopes": [],
                        "descriptionCase": null,
                        "descriptionEndMark": null,
                        "bodyRequired": false,
                        "bodyWrapWidth": null,
                        "requiredFooters": []
                    }
                }
            }
        ]
    }
}
```

However, if you provide your configuration in `composer.json`, it is not
necessary to also provide it in `captainhook.json`.

> 🚨 If using the Git commit hook functionality of Captain Hook, any
> configuration provided in `captainhook.json` will override configuration
> in `composer.json`.
>
> ⚠️ When using the standalone command (i.e. `./vendor/bin/conventional-commits`),
> only configuration in `composer.json` will apply, unless providing the
> `--config` option.

#### Configuration in a Separate File

You may also store your configuration in a separate file. For example, you may
store it in `conventional-commits.json`, like this:

``` json
{
    "typeCase": "kebab",
    "types": [
        "ci",
        "deps",
        "docs",
        "refactor",
        "style",
        "test"
    ],
    "scopeCase": "kebab",
    "scopeRequired": false,
    "scopes": [],
    "descriptionCase": "lower",
    "descriptionEndMark": "",
    "bodyRequired": true,
    "bodyWrapWidth": 72,
    "requiredFooters": ["Signed-off-by"]
}
```

When stored in a separate file, we won't know where to look for your
configuration, unless you tell us, so you must still provide a small amount of
configuration in either `composer.json` or `captainhook.json`, so we can find
it.

Here's what this looks like in `composer.json`:

``` json
{
    "extra": {
        "ramsey/conventional-commits": {
            "configFile": "./conventional-commits.json"
        }
    }
}
```

And here's what this looks like in `captainhook.json`:

``` json
{
    "commit-msg": {
        "enabled": true,
        "actions": [
            {
                "action": "\\Ramsey\\CaptainHook\\ValidateConventionalCommit",
                "options": {
                    "configFile": "./conventional-commits.json"
                }
            }
        ]
    },
    "prepare-commit-msg": {
        "enabled": true,
        "actions": [
            {
                "action": "\\Ramsey\\CaptainHook\\PrepareConventionalCommit",
                "options": {
                    "configFile": "./conventional-commits.json"
                }
            }
        ]
    }
}
```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md](SECURITY.md) for instructions on submitting a vulnerability report.

## Copyright and License

The ramsey/conventional-commits library is copyright © [Ben Ramsey](https://benramsey.com)
and licensed for use under the terms of the MIT License (MIT). Please see
[LICENSE](LICENSE) for more information.


[Conventional Commits specification]: https://www.conventionalcommits.org/en/v1.0.0/
[CaptainHook]: https://github.com/captainhookphp/captainhook
