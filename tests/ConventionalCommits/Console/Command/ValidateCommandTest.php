<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Command;

use Hamcrest\Core\IsInstanceOf;
use Mockery\MockInterface;
use Ramsey\ConventionalCommits\Console\Command\ValidateCommand;
use Ramsey\ConventionalCommits\Console\Question\MessageQuestion;
use Ramsey\ConventionalCommits\Console\SymfonyStyleFactory;
use Ramsey\Test\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateCommandTest extends TestCase
{
    public function testCommandName(): void
    {
        $command = new ValidateCommand();

        $this->assertSame('validate', $command->getName());
    }

    public function testRunWithNoArgs(): void
    {
        $input = new StringInput('');
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Validate Commit Message');

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(MessageQuestion::class))
            ->andReturn(null);

        $console
            ->expects()
            ->error('No commit message was provided');

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $command = new ValidateCommand($factory);
        $command->run($input, $output);
    }

    public function testRunWithInvalidCliArg(): void
    {
        $input = new StringInput("'some issue'");
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Validate Commit Message')->never();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(MessageQuestion::class))
            ->never();

        $console
            ->expects()
            ->error('Could not find a valid Conventional Commits message.');

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $command = new ValidateCommand($factory);
        $command->run($input, $output);
    }

    public function testRunWithValidCliArg(): void
    {
        $input = new StringInput("'fix: some issue'");
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Validate Commit Message')->never();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(MessageQuestion::class))
            ->never();

        $console->expects()->section('Commit Message');
        $console->expects()->block('fix: some issue');

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $command = new ValidateCommand($factory);
        $command->run($input, $output);
    }

    public function testRunWithInvalidInput(): void
    {
        $input = new StringInput('');
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Validate Commit Message');

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(MessageQuestion::class))
            ->andReturn('some issue');

        $console
            ->expects()
            ->error('Could not find a valid Conventional Commits message.');

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $command = new ValidateCommand($factory);
        $command->run($input, $output);
    }

    public function testRunWithValidInput(): void
    {
        $input = new StringInput('');
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Validate Commit Message');

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(MessageQuestion::class))
            ->andReturn('fix: some issue');

        $console->expects()->section('Commit Message');
        $console->expects()->block('fix: some issue');

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $command = new ValidateCommand($factory);
        $command->run($input, $output);
    }
}
