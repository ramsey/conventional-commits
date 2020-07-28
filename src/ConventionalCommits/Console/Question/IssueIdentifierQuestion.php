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

namespace Ramsey\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Message\Footer;
use Symfony\Component\Console\Question\Question;

/**
 * A prompt asking the user to enter an issue tracker identifier
 */
class IssueIdentifierQuestion extends Question
{
    private string $type;

    public function __construct(string $type)
    {
        parent::__construct(
            'Enter the issue identifier '
            . '<comment>(do not include a preceding #-symbol)</comment>',
        );

        $this->type = $type;
    }

    public function getValidator(): callable
    {
        return function (?string $answer): Footer {
            try {
                return new Footer($this->type, (string) $answer, Footer::SEPARATOR_HASH);
            } catch (InvalidArgument $exception) {
                throw new InvalidConsoleInput('Invalid identifier value. Please try again.');
            }
        };
    }
}
