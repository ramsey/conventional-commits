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
use Ramsey\ConventionalCommits\Message\Scope;
use Symfony\Component\Console\Question\Question;

use function count;
use function trim;

/**
 * A prompt asking the user the scope of this change within the overall project
 */
class ScopeQuestion extends Question implements Configurable
{
    use ConfigurableTool;

    public function __construct(?Configuration $configuration = null)
    {
        parent::__construct(
            'What is the scope of this change (e.g., component or file name)?',
        );

        $this->configuration = $configuration;
    }

    public function getValidator(): callable
    {
        return function (?string $answer): ?Scope {
            if (trim((string) $answer) === '') {
                $answer = null;
            }

            try {
                $scope = $answer === null ? null : new Scope($answer);
                $this->getConfiguration()->getMessageValidator()->validateScope($scope);
            } catch (InvalidArgument | InvalidValue $exception) {
                throw new InvalidConsoleInput('Invalid scope. ' . $exception->getMessage());
            }

            return $scope;
        };
    }

    public function getAutocompleterCallback(): ?callable
    {
        if (count($this->getConfiguration()->getScopes()) === 0) {
            return null;
        }

        return fn (): iterable => $this->getConfiguration()->getScopes();
    }
}
