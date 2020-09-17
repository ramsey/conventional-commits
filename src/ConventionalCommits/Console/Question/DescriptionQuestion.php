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

use Ramsey\ConventionalCommits\Configuration\Configurable;
use Ramsey\ConventionalCommits\Configuration\ConfigurableTool;
use Ramsey\ConventionalCommits\Configuration\Configuration;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message\Description;
use Symfony\Component\Console\Question\Question;

use function trim;

/**
 * A prompt asking the user to provide a short description (or subject)
 * for the commit message
 */
class DescriptionQuestion extends Question implements Configurable
{
    use ConfigurableTool;

    public function __construct(?Configuration $configuration = null)
    {
        parent::__construct('Write a short description of the change');
        $this->configuration = $configuration;
    }

    public function getValidator(): callable
    {
        return function (?string $answer): Description {
            if (trim((string) $answer) === '') {
                throw new InvalidConsoleInput('You must provide a short description.');
            }

            try {
                $description = new Description((string) $answer);
                $this->getConfiguration()->getMessageValidator()->validateDescription($description);
            } catch (InvalidArgument | InvalidValue $exception) {
                throw new InvalidConsoleInput('Invalid description. ' . $exception->getMessage());
            }

            return $description;
        };
    }
}
