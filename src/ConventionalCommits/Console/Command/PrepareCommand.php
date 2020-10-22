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

use Closure;
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
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;
use Ramsey\ConventionalCommits\Validator\RequiredFootersValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_merge;
use function count;

/**
 * A console command that prompts a user for input to build a valid
 * Conventional Commits commit message
 */
class PrepareCommand extends BaseCommand
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
            ->setDescription('Prepares a commit message conforming to Conventional Commits')
            ->setHelp(
                'This command interactively helps prepare a commit message '
                . 'according to the Conventional Commits specification. For more '
                . 'information, see https://www.conventionalcommits.org.',
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
        $type = $console->askQuestion(new TypeQuestion($this->getConfiguration()));

        /** @var Scope|null $scope */
        $scope = $console->askQuestion(new ScopeQuestion($this->getConfiguration()));

        /** @var Description $description */
        $description = $console->askQuestion(new DescriptionQuestion($this->getConfiguration()));

        /** @var Body|null $body */
        $body = $console->askQuestion(new BodyQuestion($this->getConfiguration()));

        /** @var Footer[] $footers */
        $footers = $this->askFooterQuestions($console);
        $footers = $this->checkRequiredFooters($console, $footers);

        $message = new Message($type, $description);
        $message->setConfiguration($this->getConfiguration());

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
            new AffectsOpenIssuesQuestion(),
            new IssueTypeQuestion(),
            fn (string $token): Question => new IssueIdentifierQuestion($token),
        ));

        $footers = array_merge($footers, $this->askFooterQuestionSection(
            $console,
            new AddFootersQuestion(),
            new FooterTokenQuestion($this->getConfiguration()),
            fn (string $token): Question => new FooterValueQuestion($token),
            count($this->getConfiguration()->getRequiredFooters()) > 0,
        ));

        return $footers;
    }

    /**
     * @return Footer[]
     *
     * @psalm-param Closure(string):Question $valueQuestionCallback
     */
    private function askFooterQuestionSection(
        SymfonyStyle $console,
        Question $decisionPathQuestion,
        Question $tokenQuestion,
        Closure $valueQuestionCallback,
        bool $isRequired = false
    ): array {
        if (!$isRequired && !$console->askQuestion($decisionPathQuestion)) {
            return [];
        }

        $footers = [];

        do {
            /** @var Footer|null $footer */
            $footer = $this->askFooterQuestion($console, $tokenQuestion, $valueQuestionCallback);

            if ($footer !== null) {
                $footers[] = $footer;
            }
        } while ($footer !== null);

        return $footers;
    }

    /**
     * @psalm-param Closure(string):Question $valueQuestionCallback
     */
    private function askFooterQuestion(
        SymfonyStyle $console,
        Question $tokenQuestion,
        Closure $valueQuestionCallback
    ): ?Footer {
        /** @var string|null $token */
        $token = $console->askQuestion($tokenQuestion);

        if ($token === null) {
            return null;
        }

        /** @var Footer|null $footer */
        $footer = $console->askQuestion($valueQuestionCallback($token));

        return $footer;
    }

    /**
     * @param Footer[] $footers
     *
     * @return Footer[]
     */
    private function checkRequiredFooters(SymfonyStyle $console, array $footers): array
    {
        $validator = new RequiredFootersValidator();
        $validator->setConfiguration($this->getConfiguration());

        try {
            $validator->isValidOrException($footers);
        } catch (InvalidValue $exception) {
            $console->error($exception->getMessage());

            $footers = array_merge($footers, $this->askFooterQuestionSection(
                $console,
                new AddFootersQuestion(),
                new FooterTokenQuestion($this->getConfiguration()),
                fn (string $token): Question => new FooterValueQuestion($token),
                count($this->getConfiguration()->getRequiredFooters()) > 0,
            ));

            $footers = $this->checkRequiredFooters($console, $footers);
        }

        return $footers;
    }
}
