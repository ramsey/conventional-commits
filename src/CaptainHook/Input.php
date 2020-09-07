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

namespace Ramsey\CaptainHook;

use CaptainHook\App\Console\IO;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

use function reset;

/**
 * Wraps CaptainHook console IO for use with symfony/console
 */
class Input implements InputInterface
{
    private IO $captainHookIO;

    public function __construct(IO $captainHookIO)
    {
        $this->captainHookIO = $captainHookIO;
    }

    /**
     * @inheritDoc
     */
    public function getFirstArgument()
    {
        $arguments = $this->getArguments();

        /** @var string|false $firstArgument */
        $firstArgument = reset($arguments);

        return $firstArgument === false ? null : (string) $firstArgument;
    }

    /**
     * @inheritDoc
     */
    public function hasParameterOption($values, bool $onlyParams = false)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getParameterOption($values, $default = false, bool $onlyParams = false)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    public function bind(InputDefinition $definition): void
    {
        // Do nothing. CaptainHook IO is already bound.
    }

    public function validate(): void
    {
        // Do nothing. CaptainHook IO is already validated.
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return $this->captainHookIO->getArguments();
    }

    /**
     * @inheritDoc
     */
    public function getArgument(string $name)
    {
        if (!$this->hasArgument($name)) {
            throw new InvalidArgumentException("Argument '{$name}' does not exist");
        }

        /** @var string|string[]|null $value */
        $value = $this->getArguments()[$name];

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setArgument(string $name, $value): void
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function hasArgument($name): bool
    {
        return isset($this->getArguments()[$name]);
    }

    public function getOptions()
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    public function getOption(string $name)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function setOption(string $name, $value): void
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    public function hasOption(string $name)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    public function isInteractive(): bool
    {
        return $this->captainHookIO->isInteractive();
    }

    public function setInteractive(bool $interactive): void
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    private function unsupportedMethod(string $methodName): RuntimeException
    {
        return new RuntimeException(
            "{$methodName} is not supported in this implementation",
        );
    }
}
