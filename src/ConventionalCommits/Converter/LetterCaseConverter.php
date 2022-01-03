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

namespace Ramsey\ConventionalCommits\Converter;

use Jawira\CaseConverter\CaseConverterInterface;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\String\LetterCase;

use function gettype;
use function in_array;
use function is_string;
use function sprintf;
use function ucfirst;

/**
 * A converter that converts a string value to a given letter case
 */
class LetterCaseConverter implements Convertible
{
    private CaseConverterInterface $caseConverter;
    private ?string $case;

    public function __construct(CaseConverterInterface $caseConverter, ?string $case)
    {
        if ($case !== null && !in_array($case, LetterCase::CASES)) {
            throw new InvalidArgument("'$case' is not a valid letter case.");
        }

        $this->case = $case;
        $this->caseConverter = $caseConverter;
    }

    public function getCase(): ?string
    {
        return $this->case;
    }

    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        if ($this->case === null) {
            return $value;
        }

        if (!is_string($value)) {
            throw new InvalidArgument(sprintf(
                "The value must be a string; received '%s'",
                gettype($value),
            ));
        }

        $convertToMethod = 'to' . ucfirst($this->case);

        return $this->caseConverter->convert($value)->{$convertToMethod}();
    }
}
