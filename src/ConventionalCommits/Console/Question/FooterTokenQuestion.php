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
use Ramsey\ConventionalCommits\Message\Footer;
use Symfony\Component\Console\Question\Question;

use function count;
use function strlen;
use function trim;

/**
 * A prompt asking the user to enter a footer token
 */
class FooterTokenQuestion extends Question implements Configurable
{
    use ConfigurableTool;

    public function __construct(?Configuration $configuration = null)
    {
        parent::__construct(
            'To add a footer, provide a footer name, or press ENTER to skip (e.g., Signed-off-by)',
        );

        $this->configuration = $configuration;
    }

    public function getValidator(): callable
    {
        return function (?string $answer): ?string {
            if ($answer === null || strlen(trim($answer)) === 0) {
                return null;
            }

            try {
                $validFooter = new Footer($answer, 'validation');
            } catch (InvalidArgument | InvalidValue $exception) {
                throw new InvalidConsoleInput('Invalid footer name. ' . $exception->getMessage());
            }

            return $validFooter->getToken();
        };
    }

    public function getAutocompleterCallback(): ?callable
    {
        if (count($this->getConfiguration()->getRequiredFooters()) === 0) {
            return null;
        }

        return fn (): iterable => $this->getConfiguration()->getRequiredFooters();
    }
}
