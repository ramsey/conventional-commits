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

use Ramsey\ConventionalCommits\Console\Question\MessageQuestion;
use Ramsey\ConventionalCommits\Console\SymfonyStyleFactory;
use Ramsey\ConventionalCommits\Exception\ConventionalException;
use Ramsey\ConventionalCommits\Parser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command that validates a commit message as per the
 * Conventional Commits specification
 */
class ValidateCommand extends BaseCommand
{
    private SymfonyStyleFactory $styleFactory;

    public function __construct(?SymfonyStyleFactory $styleFactory = null)
    {
        parent::__construct('validate');

        $this->styleFactory = $styleFactory ?? new SymfonyStyleFactory();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Validate a commit message to ensure it conforms to Conventional Commits')
            ->setHelp(
                'This command validates a commit message according to the '
                . 'Conventional Commits specification. For more information, '
                . 'see https://www.conventionalcommits.org.',
            )
            ->addArgument(
                'message',
                InputArgument::OPTIONAL,
                'The commit message to be validated',
            )
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to a file containing Conventional Commits configuration',
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $console = $this->styleFactory->factory($input, $output);

        /** @var string|null $message */
        $message = $input->getArgument('message');
        if ($message === null) {
            $console->title('Validate Commit Message');
            /** @var string|null $message */
            $message = $console->askQuestion(new MessageQuestion($this->getConfiguration()));
        }

        if ($message === null) {
            $console->error('No commit message was provided');

            return self::FAILURE;
        }

        try {
            $parser = new Parser($this->getConfiguration());
            $parser->parse($message);
        } catch (ConventionalException $exception) {
            $console->error($exception->getMessage());

            return self::FAILURE;
        }

        $console->section('Commit Message');
        $console->block($message);

        return self::SUCCESS;
    }
}
