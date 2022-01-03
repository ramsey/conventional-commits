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
use Ramsey\ConventionalCommits\Message\Scope;

use function array_map;
use function count;
use function gettype;
use function implode;
use function in_array;
use function is_string;
use function sprintf;
use function strtolower;

/**
 * Validates whether the scope is in the list of configured scopes
 */
class ScopeValidator implements Configurable, Validator
{
    use ConfigurableTool;

    /**
     * @inheritDoc
     */
    public function isValid($value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgument(sprintf(
                "The value must be a string; received '%s'",
                gettype($value),
            ));
        }

        if (!$this->isInConfiguredScopes($value)) {
            return false;
        }

        try {
            new Scope($value);
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

        /** @var string $guaranteedStringValue */
        $guaranteedStringValue = $value;

        if ($this->getConfiguredScopes()) {
            throw new InvalidValue(sprintf(
                "'%s' is not one of the valid scopes '%s'.",
                $guaranteedStringValue,
                implode(', ', $this->getConfiguredScopes()),
            ));
        }

        throw new InvalidValue(sprintf(
            "'%s' is not a valid scope value.",
            $guaranteedStringValue,
        ));
    }

    private function isInConfiguredScopes(string $value): bool
    {
        if (count($this->getConfiguredScopes()) === 0) {
            return true;
        }

        return in_array(strtolower($value), $this->getConfiguredScopes());
    }

    /**
     * @return string[]
     */
    private function getConfiguredScopes(): array
    {
        if (count($this->getConfiguration()->getScopes()) === 0) {
            return [];
        }

        return array_map(
            fn (string $v): string => strtolower($v),
            $this->getConfiguration()->getScopes(),
        );
    }
}
