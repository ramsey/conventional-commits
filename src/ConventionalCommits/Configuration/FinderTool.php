<?php

/**
 * This file is part of ramsey/conventional-commits
 *
 * ramsey/conventional-commits is open source software: you can distribute it
 * and/or modify it under the terms of the MIT License (the "License"). You may
 * not use this file except in compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\ConventionalCommits\Configuration;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use JsonException;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use Ramsey\ConventionalCommits\Exception\ComposerNotFound;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use function array_walk;
use function dirname;
use function file_get_contents;
use function gettype;
use function implode;
use function is_array;
use function json_decode;
use function realpath;
use function sprintf;
use function ucfirst;

use const JSON_THROW_ON_ERROR;

/**
 * This tool provides functionality for finding configuration in appropriate
 * locations and returning a DefaultConfiguration instance
 */
trait FinderTool
{
    /**
     * Returns a Configuration instance after looking for configuration in various locations
     *
     * @param mixed[]|null $options
     *
     * @throws JsonException
     */
    public function findConfiguration(
        InputInterface $input,
        OutputInterface $output,
        ?array $options = null
    ): Configuration {
        /** @var mixed[] | null $config */
        $config = $options['config'] ?? null;

        /** @var string | null $configFile */
        $configFile = $options['configFile'] ?? null;

        // If provided config, it takes precedence.
        if ($config !== null && $this->validateConfig((object) $config)) {
            return new DefaultConfiguration($config);
        }

        // If provided with a config file, it has second-highest precedence.
        if ($configFile !== null) {
            return new DefaultConfiguration($this->loadConfigFromFile($configFile));
        }

        // Otherwise, attempt to load configuration from composer.json.
        return new DefaultConfiguration($this->loadConfigFromComposer($input, $output));
    }

    /**
     * Returns the Composer instance for the current project
     */
    public function getComposer(
        InputInterface $input,
        OutputInterface $output,
        Filesystem $filesystem
    ): Composer {
        $composerJson = $this->findComposerJson($filesystem);

        $composerIO = new ConsoleIO($input, $output, new HelperSet());
        $composerFactory = new Factory();

        return $composerFactory->createComposer($composerIO, $composerJson, true, null, false);
    }

    /**
     * @return mixed[]
     *
     * @throws InvalidArgument if unable to read the file
     * @throws JsonException if an error occurs while decoding the config file JSON
     */
    private function loadConfigFromFile(string $file): array
    {
        $contents = @file_get_contents($file);
        if ($contents === false) {
            throw new InvalidArgument(sprintf('Could not find config file \'%s\'', $file));
        }

        /** @var mixed $value */
        $value = json_decode($contents, true, 4, JSON_THROW_ON_ERROR);

        if (!is_array($value)) {
            throw new InvalidValue(sprintf(
                'Expected a configuration array in %s; received %s instead.',
                $file,
                gettype($value),
            ));
        }

        $this->validateConfig((object) $value);

        return $value;
    }

    /**
     * @return mixed[]
     *
     * @throws JsonException
     */
    private function loadConfigFromComposer(InputInterface $input, OutputInterface $output): array
    {
        $composer = $this->getComposer($input, $output, new Filesystem());

        /** @var mixed|null $config */
        $config = $composer->getPackage()->getExtra()['ramsey/conventional-commits']['config'] ?? null;

        /** @var mixed|null $configFile */
        $configFile = $composer->getPackage()->getExtra()['ramsey/conventional-commits']['configFile'] ?? null;

        if ($config !== null && !is_array($config)) {
            throw new InvalidValue(sprintf(
                'Expected a configuration array in composer.json '
                . 'extra.ramsey/conventional-commits.config; received %s instead.',
                gettype($config),
            ));
        }

        if ($config !== null && $this->validateConfig((object) $config)) {
            return $config;
        }

        if ($configFile !== null) {
            return $this->loadConfigFromFile((string) $configFile);
        }

        return [];
    }

    /**
     * @throws InvalidValue if configuration does not validate against schema
     */
    private function validateConfig(object $config): bool
    {
        $validator = new Validator();
        $schema = Schema::fromJsonString((string) file_get_contents(__DIR__ . '/../../../schema.json'));
        $result = $validator->schemaValidation($config, $schema);

        if ($result->hasErrors() && $result->getFirstError() !== null) {
            /** @var ValidationError $error */
            $error = $result->getFirstError();

            $issues = $error->keywordArgs();
            array_walk($issues, function (string &$v, string $k): void {
                $v = "{$k} {$v}";
            });

            throw new InvalidValue(ucfirst(sprintf(
                '%s value found in configuration; %s.',
                (string) $error->data(),
                implode(', ', $issues),
            )));
        }

        return true;
    }

    /**
     * @throws ComposerNotFound when unable to find a suitable composer.json
     */
    private function findComposerJson(Filesystem $filesystem): string
    {
        // We do this as a way to determine where the package is installed, so
        // we can determine which composer.json to use. This isn't fool-proof,
        // since we can't detect when config.vendor-dir puts the vendor
        // directory in a different location.
        $composerAutoloadLocations = [
            // This case means we're installed in the vendor directory, so we'll
            // look for composer.json above this location.
            __DIR__ . '/../../../../../autoload.php',

            // This case means we're working on ramsey/conventional-commits, so
            // we'll use the composer.json above this location.
            __DIR__ . '/../../../vendor/autoload.php',
        ];

        foreach ($composerAutoloadLocations as $file) {
            if ($filesystem->exists($file)) {
                $path = dirname((string) realpath($file));

                break;
            }
        }

        if (!isset($path)) {
            throw new ComposerNotFound(
                'Could not find the autoloader. Did you run composer install or composer update?',
            );
        }

        do {
            if ($filesystem->exists((string) $path . '/composer.json')) {
                $composerJson = (string) realpath((string) $path . '/composer.json');
            }

            // We have reached the root directory.
            if (dirname((string) $path) === $path) {
                throw new ComposerNotFound('Could not find composer.json.');
            }

            $path = dirname((string) $path);
        } while (!isset($composerJson));

        return (string) $composerJson;
    }
}
