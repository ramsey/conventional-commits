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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

use function array_keys;

/**
 * @deprecated This class is no longer used and will be removed in the
 *     next major release.
 */
class Input extends ArrayInput
{
    public function __construct(IO $captainHookIO)
    {
        $definition = new InputDefinition();

        /**
         * @psalm-suppress UnnecessaryVarAnnotation
         * @var string $key
         */
        foreach (array_keys($captainHookIO->getArguments()) as $key) {
            $definition->addArgument(new InputArgument($key));
        }

        parent::__construct($captainHookIO->getArguments(), $definition);
        $this->setInteractive($captainHookIO->isInteractive());
    }
}
