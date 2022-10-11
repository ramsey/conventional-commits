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
use Symfony\Component\Console\Question\Question;

use function method_exists;
use function trim;

/**
 * A prompt that accepts long-form body content for the commit message
 */
class MessageQuestion extends Question implements Configurable
{
    use ConfigurableTool;

    public function __construct(?Configuration $configuration = null)
    {
        if (method_exists($this, 'setMultiline')) {
            $this->setMultiline(true); // @codeCoverageIgnore
        }

        $this->configuration = $configuration;

        parent::__construct(
            'Enter the commit message to be validated',
        );
    }

    public function getValidator(): callable
    {
        return function (?string $answer): ?string {
            if (trim((string) $answer) === '') {
                return null;
            }

            return $answer;
        };
    }
}
