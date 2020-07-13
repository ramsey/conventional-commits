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

use function wordwrap;

use const PHP_EOL;

/**
 * A Conventional Commits body
 *
 * From the Conventional Commits 1.0.0 specification:
 *
 * > 6. A longer commit body MAY be provided after the short description,
 * > providing additional contextual information about the code changes. The
 * > body MUST begin one blank line after the description.
 * >
 * > 7. A commit body is free-form and MAY consist of any number of newline
 * > separated paragraphs.
 *
 * @link https://www.conventionalcommits.org/en/v1.0.0/#specification Conventional Commits
 */
class Body extends Text
{
    private const BODY_WIDTH = 72;

    public function __construct(string $text)
    {
        parent::__construct(wordwrap($text, self::BODY_WIDTH, PHP_EOL));
    }
}
