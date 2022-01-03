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

        return $firstArgument === false ? null : $firstArgument;
    }

    /**
     * @param string | mixed[] $values
     *
     * @inheritDoc
     */
    public function hasParameterOption($values, bool $onlyParams = false)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @param string | mixed[] $values
     * @param string | bool | int | float | mixed[] | null $default
     *
     * @inheritDoc
     */
    public function getParameterOption($values, $default = false, bool $onlyParams = false)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @return void
     */
    public function bind(InputDefinition $definition)
    {
        // Do nothing. CaptainHook IO is already bound.
    }

    /**
     * @return void
     */
    public function validate()
    {
        // Do nothing. CaptainHook IO is already validated.
    }

    /**
     * @return array<string | bool | int | float | mixed[] | null>
     */
    public function getArguments()
    {
        /** @var array<string | bool | int | float | mixed[] | null> */
        return $this->captainHookIO->getArguments();
    }

    /**
     * @inheritDoc
     */
    public function getArgument(string $name)
    {
        if (!$this->hasArgument($name)) {
            throw new InvalidArgumentException("Argument '$name' does not exist.");
        }

        /** @var string | string[] | null */
        return $this->getArguments()[$name];
    }

    /**
     * @param mixed $value
     *
     * @return void
     *
     * @inheritDoc
     */
    public function setArgument(string $name, $value)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function hasArgument(string $name)
    {
        return isset($this->getArguments()[$name]);
    }

    /**
     * @return array<string | bool | int | float | mixed[] | null>
     */
    public function getOptions()
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $name)
    {
        return null;
    }

    /**
     * @param mixed $value
     *
     * @return void
     *
     * @inheritDoc
     */
    public function setOption(string $name, $value)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    public function hasOption(string $name)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function isInteractive()
    {
        return $this->captainHookIO->isInteractive();
    }

    /**
     * @return void
     */
    public function setInteractive(bool $interactive)
    {
        throw $this->unsupportedMethod(__METHOD__);
    }

    private function unsupportedMethod(string $methodName): RuntimeException
    {
        return new RuntimeException(
            "$methodName is not supported in this implementation",
        );
    }
}
