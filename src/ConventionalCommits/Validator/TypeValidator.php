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

namespace Ramsey\ConventionalCommits\Validator;

use Ramsey\ConventionalCommits\Configuration\Configurable;
use Ramsey\ConventionalCommits\Configuration\ConfigurableTool;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message\Type;

use function array_map;
use function array_merge;
use function count;
use function implode;
use function in_array;
use function sprintf;
use function strtolower;

/**
 * Validates whether the type is in the list of configured types
 */
class TypeValidator implements Configurable, Validator
{
    use ConfigurableTool;

    /**
     * @inheritDoc
     */
    public function isValid($value): bool
    {
        if (!$this->isInConfiguredTypes((string) $value)) {
            return false;
        }

        try {
            new Type((string) $value);
        } catch (InvalidArgument $exception) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isValidOrException($value): bool
    {
        if ($this->isValid($value)) {
            return true;
        }

        if ($this->getConfiguredTypes()) {
            throw new InvalidValue(sprintf(
                "'%s' is not one of the valid types '%s'.",
                (string) $value,
                implode(', ', $this->getConfiguredTypes()),
            ));
        }

        throw new InvalidValue(sprintf(
            "'%s' is not a valid type value.",
            (string) $value,
        ));
    }

    private function isInConfiguredTypes(string $value): bool
    {
        if (count($this->getConfiguredTypes()) === 0) {
            return true;
        }

        return in_array(strtolower($value), $this->getConfiguredTypes());
    }

    /**
     * @return string[]
     */
    private function getConfiguredTypes(): array
    {
        if (count($this->getConfiguration()->getTypes()) === 0) {
            return [];
        }

        // "feat" and "fix" will always be valid types, according to the spec.
        return array_merge(
            ['feat', 'fix'],
            array_map(
                fn (string $v): string => strtolower($v),
                $this->getConfiguration()->getTypes(),
            ),
        );
    }
}
