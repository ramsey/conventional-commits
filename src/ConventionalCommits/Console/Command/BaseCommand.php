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

namespace Ramsey\ConventionalCommits\Console\Command;

use Ramsey\ConventionalCommits\Configuration\Configurable;
use Ramsey\ConventionalCommits\Configuration\ConfigurableTool;
use Ramsey\ConventionalCommits\Configuration\FinderTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides common functionality to ramsey/conventional-commits commands
 */
abstract class BaseCommand extends Command implements Configurable
{
    use ConfigurableTool;
    use FinderTool;

    public const SUCCESS = 0;
    public const FAILURE = 1;

    /**
     * Children should implement doExecute() to provide command functionality
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output): int;

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = null;

        /** @var string|null $configFile */
        $configFile = $input->getOption('config');

        if ($configFile !== null) {
            $config = ['configFile' => $configFile];
        }

        if ($config !== null || $this->configuration === null) {
            $this->setConfiguration($this->findConfiguration($input, $output, $config));
        }

        return $this->doExecute($input, $output);
    }
}
