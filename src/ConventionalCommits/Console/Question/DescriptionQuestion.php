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
use Ramsey\ConventionalCommits\Message\Description;
use Symfony\Component\Console\Question\Question;

/**
 * A prompt asking the user to provide a short description (or subject)
 * for the commit message
 */
class DescriptionQuestion extends Question
{
    public function __construct()
    {
        parent::__construct('Write a short description of the change');

        if (method_exists($this, 'setMultiline')) {
            $this->setMultiline(true);
        }
    }

    public function getValidator(): callable
    {
        return function (?string $answer): Description {
            try {
                return new Description((string) $answer);
            } catch (InvalidArgument $exception) {
                throw new InvalidConsoleInput('Invalid description. Please try again.');
            }
        };
    }
}
