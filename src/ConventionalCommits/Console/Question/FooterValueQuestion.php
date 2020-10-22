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
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message\Footer;
use Symfony\Component\Console\Question\Question;

/**
 * A prompt asking the user to enter a value for the given footer token
 */
class FooterValueQuestion extends Question
{
    private string $token;

    public function __construct(string $token)
    {
        parent::__construct('Provide a value for the footer ' . $token);

        $this->token = $token;
    }

    public function getValidator(): callable
    {
        return function (?string $answer): Footer {
            try {
                return new Footer($this->token, (string) $answer);
            } catch (InvalidArgument | InvalidValue $exception) {
                throw new InvalidConsoleInput('Invalid footer value. ' . $exception->getMessage());
            }
        };
    }
}
