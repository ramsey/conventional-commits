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
use Ramsey\ConventionalCommits\Message\Footer;

use function array_diff;
use function array_filter;
use function array_intersect;
use function array_map;
use function implode;
use function is_array;
use function sprintf;
use function strtolower;

/**
 * Validates whether all required footers are present, if applicable
 */
class RequiredFootersValidator implements Configurable, Validator
{
    use ConfigurableTool;

    /**
     * @var string[]|null
     */
    private ?array $requiredFooters = null;

    /**
     * @inheritDoc
     */
    public function isValid($value): bool
    {
        $this->checkValue($value);

        if ($this->getRequiredFooters() === []) {
            return true;
        }

        /** @var Footer[] $footers */
        $footers = $value;

        return $this->getPresentRequiredFooters($footers) === $this->getRequiredFooters();
    }

    /**
     * @inheritDoc
     */
    public function isValidOrException($value): bool
    {
        if ($this->isValid($value)) {
            return true;
        }

        /** @var Footer[] $footers */
        $footers = $value;

        $missingFooters = array_diff(
            $this->getRequiredFooters(),
            $this->getPresentRequiredFooters($footers),
        );

        throw new InvalidValue(sprintf(
            'Please provide the following required footers: %s.',
            implode(', ', $missingFooters),
        ));
    }

    /**
     * @param Footer[] $footers
     *
     * @return string[]
     */
    private function getPresentRequiredFooters(array $footers): array
    {
        $presentFooters = array_map(fn (Footer $v): string => strtolower($v->getToken()), $footers);

        return array_intersect($this->getRequiredFooters(), $presentFooters);
    }

    /**
     * @return string[]
     */
    private function getRequiredFooters(): array
    {
        if ($this->requiredFooters === null) {
            $requiredFooters = array_map(
                fn (string $v): string => strtolower($v),
                $this->getConfiguration()->getRequiredFooters(),
            );

            $this->requiredFooters = $requiredFooters;
        }

        return $this->requiredFooters;
    }

    /**
     * @param mixed $value
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    private function checkValue($value): void
    {
        $isFooter = /** @param mixed $v */ fn ($v): bool => $v instanceof Footer;

        if (is_array($value) && array_filter($value, $isFooter) === $value) {
            return;
        }

        throw new InvalidArgument(sprintf(
            '\$value must be an array of %s.',
            Footer::class,
        ));
    }
}
