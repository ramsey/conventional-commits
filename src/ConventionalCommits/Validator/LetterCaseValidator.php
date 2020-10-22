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
use Ramsey\ConventionalCommits\Exception\InvalidValue;

use function sprintf;

/**
 * Validates whether a string conforms to the specified letter case rules
 */
class LetterCaseValidator implements Configurable, Validator
{
    use ConfigurableTool;

    private ?string $case;

    public function __construct(?string $case)
    {
        $this->case = $case;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value): bool
    {
        $converter = $this->getConfiguration()->getLetterCaseConverter($this->case);

        return $value === $converter->convert($value);
    }

    /**
     * @inheritDoc
     */
    public function isValidOrException($value): bool
    {
        if ($this->isValid($value)) {
            return true;
        }

        throw new InvalidValue(sprintf(
            "'%s' is not formatted in %s case.",
            (string) $value,
            (string) $this->case,
        ));
    }
}
