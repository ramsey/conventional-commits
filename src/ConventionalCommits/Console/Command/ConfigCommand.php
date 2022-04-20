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

use Ramsey\ConventionalCommits\Console\SymfonyStyleFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * A console command allowing for configuration of ramsey/conventional-commits
 */
class ConfigCommand extends BaseCommand
{
    private SymfonyStyleFactory $styleFactory;

    public function __construct(?SymfonyStyleFactory $styleFactory = null)
    {
        parent::__construct('config');

        $this->styleFactory = $styleFactory ?? new SymfonyStyleFactory();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(
                'Configures options for creating Conventional Commits',
            )
            ->setHelp(
                'Currently, this command provides only the --dump option to '
                . 'print the current configuration',
            )
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to a file containing Conventional Commits configuration',
            )
            ->addOption(
                'dump',
                null,
                InputOption::VALUE_NONE,
                'Print the current configuration to STDOUT as JSON',
                null,
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $console = $this->styleFactory->factory($input, $output);

        if ($input->getOption('dump') !== false) {
            $console->writeln((string) json_encode(
                $this->getConfiguration(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            ));
        }

        return self::SUCCESS;
    }
}
