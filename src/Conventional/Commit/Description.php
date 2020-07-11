<?php

/**
 * This file is part of ramsey/captainhook-conventional
 *
 * ramsey/captainhook-conventional is open source software: you can distribute
 * it and/or modify it under the terms of the MIT License (the "License"). You
 * may not use this file except in compliance with the License.
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

namespace Ramsey\Conventional\Commit;

use Ramsey\Conventional\Exception\InvalidArgument;

use function preg_match;

/**
 * A Conventional Commits body
 *
 * From the Conventional Commits 1.0.0 specification:
 *
 * > 5. A description MUST immediately follow the colon and space after the
 * > type/scope prefix. The description is a short summary of the code changes,
 * > e.g., *fix: array parsing issue when multiple spaces were contained in
 * > string*.
 *
 * @link https://www.conventionalcommits.org/en/v1.0.0/#specification Conventional Commits
 */
class Description extends Text
{
    private const DESCRIPTION_PATTERN = '/^[[:print:]]+$/u';

    public function __construct(string $text)
    {
        if (!preg_match(self::DESCRIPTION_PATTERN, $text)) {
            throw new InvalidArgument(
                'Description may not contain any control characters',
            );
        }

        parent::__construct(trim($text));
    }
}
