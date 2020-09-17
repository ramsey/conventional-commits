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

namespace Ramsey\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Validator\ValidatableTool;

use function array_pop;
use function explode;
use function preg_match;
use function trim;

/**
 * A noun for use with Conventional Commits
 */
abstract class Noun implements Unit
{
    use ValidatableTool;

    private const NOUN_PATTERN = '/^[a-zA-Z0-9][\w-]+$/u';

    private string $noun;

    public function __construct(string $noun)
    {
        if (!preg_match(self::NOUN_PATTERN, $noun)) {
            $nameSegments = explode('\\', static::class);
            $entity = array_pop($nameSegments);

            throw new InvalidArgument(
                $entity . 's must contain only alphanumeric characters, underscores, and dashes.',
            );
        }

        $this->noun = trim($noun);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->noun;
    }
}
