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

use Ramsey\ConventionalCommits\Console\Question\AddFootersQuestion;
use Ramsey\ConventionalCommits\Console\Question\AffectsOpenIssuesQuestion;
use Ramsey\ConventionalCommits\Console\Question\BodyQuestion;
use Ramsey\ConventionalCommits\Console\Question\DescribeBreakingChangesQuestion;
use Ramsey\ConventionalCommits\Console\Question\DescriptionQuestion;
use Ramsey\ConventionalCommits\Console\Question\FooterTokenQuestion;
use Ramsey\ConventionalCommits\Console\Question\FooterValueQuestion;
use Ramsey\ConventionalCommits\Console\Question\HasBreakingChangesQuestion;
use Ramsey\ConventionalCommits\Console\Question\IssueIdentifierQuestion;
use Ramsey\ConventionalCommits\Console\Question\IssueTypeQuestion;
use Ramsey\ConventionalCommits\Console\Question\ScopeQuestion;
use Ramsey\ConventionalCommits\Console\Question\TypeQuestion;
use Ramsey\ConventionalCommits\Console\SymfonyStyleFactory;
use Ramsey\ConventionalCommits\Message;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_merge;

/**
 * A console command that prompts a user for input to build a valid
 * Conventional Commits commit message
 */
class PrepareCommand extends Command
{
    private ?Message $message = null;
    private SymfonyStyleFactory $styleFactory;

    public function __construct(?SymfonyStyleFactory $styleFactory = null)
    {
        parent::__construct('prepare');

        $this->styleFactory = $styleFactory ?? new SymfonyStyleFactory();
    }

    /**
     * Returns the Conventional Commits commit message created by this command
     * or null if a commit message does not exist
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Prepares a commit message.')
            ->setHelp(
                'This command helps prepare a commit message according to the '
                . 'Conventional Commits specification.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $console = $this->styleFactory->factory($input, $output);
        $console->title('Prepare Commit Message');
        $console->text([
            'The following prompts will help you create a commit message that',
            'follows the <href=https://www.conventionalcommits.org/en/v1.0.0/>Conventional Commits</> specification.',
        ]);

        $this->message = $this->askQuestions($console);

        $console->section('Commit Message');
        $console->block($this->message->toString());

        return Command::SUCCESS;
    }

    /**
     * Steps through each question to prompt the user and returns a
     * Conventional Commits commit message
     */
    private function askQuestions(SymfonyStyle $console): Message
    {
        /** @var Type $type */
        $type = $console->askQuestion(new TypeQuestion());

        /** @var Scope|null $scope */
        $scope = $console->askQuestion(new ScopeQuestion());

        /** @var Description $description */
        $description = $console->askQuestion(new DescriptionQuestion());

        /** @var Body|null $body */
        $body = $console->askQuestion(new BodyQuestion());

        /** @var Footer[] $footers */
        $footers = $this->askFooterQuestions($console);

        $message = new Message($type, $description);

        if ($scope !== null) {
            $message->setScope($scope);
        }

        if ($body !== null) {
            $message->setBody($body);
        }

        foreach ($footers as $footer) {
            $message->addFooter($footer);
        }

        return $message;
    }

    /**
     * Prompts the user with questions to build a commit message footer
     *
     * @return Footer[]
     */
    private function askFooterQuestions(SymfonyStyle $console): array
    {
        $footers = [];

        if ($console->askQuestion(new HasBreakingChangesQuestion())) {
            /** @var Footer $breakingChanges */
            $breakingChanges = $console->askQuestion(new DescribeBreakingChangesQuestion());
            $footers[] = $breakingChanges;
        }

        $footers = array_merge($footers, $this->askFooterQuestionSection(
            $console,
            AffectsOpenIssuesQuestion::class,
            IssueTypeQuestion::class,
            IssueIdentifierQuestion::class,
        ));

        $footers = array_merge($footers, $this->askFooterQuestionSection(
            $console,
            AddFootersQuestion::class,
            FooterTokenQuestion::class,
            FooterValueQuestion::class,
        ));

        return $footers;
    }

    /**
     * @param class-string<Question> $decisionPathQuestionClass
     * @param class-string<Question> $tokenQuestionClass
     * @param class-string<Question> $valueQuestionClass
     *
     * @return Footer[]
     */
    private function askFooterQuestionSection(
        SymfonyStyle $console,
        string $decisionPathQuestionClass,
        string $tokenQuestionClass,
        string $valueQuestionClass
    ): array {
        if (!$console->askQuestion(new $decisionPathQuestionClass())) {
            return [];
        }

        $footers = [];

        do {
            /** @var Footer|null $footer */
            $footer = $this->askFooterQuestion($console, $tokenQuestionClass, $valueQuestionClass);

            if ($footer !== null) {
                $footers[] = $footer;
            }
        } while ($footer !== null);

        return $footers;
    }

    /**
     * @param class-string<Question> $tokenQuestionClass
     * @param class-string<Question> $valueQuestionClass
     */
    private function askFooterQuestion(
        SymfonyStyle $console,
        string $tokenQuestionClass,
        string $valueQuestionClass
    ): ?Footer {
        /** @var string|null $token */
        $token = $console->askQuestion(new $tokenQuestionClass());

        if ($token === null) {
            return null;
        }

        /** @var Footer|null $footer */
        $footer = $console->askQuestion(new $valueQuestionClass($token));

        return $footer;
    }
}
