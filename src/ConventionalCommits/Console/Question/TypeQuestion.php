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
use Ramsey\ConventionalCommits\Message\Type;
use Symfony\Component\Console\Question\Question;

use function array_merge;

/**
 * A prompt asking the user to identify the type of change they are committing
 */
class TypeQuestion extends Question implements Configurable
{
    use ConfigurableTool;

    public function __construct(?Configuration $configuration = null)
    {
        parent::__construct(
            'What is the type of change you\'re committing? (e.g., feat, fix, etc.)',
            'feat',
        );

        $this->configuration = $configuration;
    }

    public function getValidator(): callable
    {
        return function (?string $answer): Type {
            try {
                $type = new Type((string) $answer);
                $this->getConfiguration()->getMessageValidator()->validateType($type);
            } catch (InvalidArgument | InvalidValue $exception) {
                throw new InvalidConsoleInput('Invalid type. ' . $exception->getMessage());
            }

            return $type;
        };
    }

    public function getAutocompleterCallback(): callable
    {
        return fn (): iterable => array_merge(
            ['feat', 'fix'],
            $this->getConfiguration()->getTypes(),
        );
    }
}
