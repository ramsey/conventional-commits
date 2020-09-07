<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Command;

use Mockery\MockInterface;
use Ramsey\ConventionalCommits\Console\Command\PrepareCommand;
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
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

use function anInstanceOf;
use function preg_replace;

use const PHP_EOL;

class PrepareCommandTest extends TestCase
{
    public function testCommandName(): void
    {
        $command = new PrepareCommand();

        $this->assertSame('prepare', $command->getName());
    }

    public function testGetMessageReturnsNullForNewCommand(): void
    {
        $command = new PrepareCommand();

        $this->assertNull($command->getMessage());
    }

    public function testRun(): void
    {
        $expectedMessage = <<<'EOD'
            feat(component)!: this is a commit summary

            this is a commit body

            BREAKING CHANGE: something broke
            fix #1234
            re #4321
            Signed-off-by: Janet Doe <jdoe@example.com>
            See-also: abcdef0123456789

            EOD;

        // Fix line endings in case running tests on Windows.
        $expectedMessage = preg_replace('/(?<!\r)\n/', PHP_EOL, $expectedMessage);

        $input = new StringInput('');
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Prepare Commit Message');
        $console->expects()->text([
            'The following prompts will help you create a commit message that',
            'follows the <href=https://www.conventionalcommits.org/en/v1.0.0/>Conventional Commits</> specification.',
        ]);

        $console
            ->expects()
            ->askQuestion(anInstanceOf(TypeQuestion::class))
            ->andReturn(new Type('feat'));

        $console
            ->expects()
            ->askQuestion(anInstanceOf(ScopeQuestion::class))
            ->andReturn(new Scope('component'));

        $console
            ->expects()
            ->askQuestion(anInstanceOf(DescriptionQuestion::class))
            ->andReturn(new Description('this is a commit summary'));

        $console
            ->expects()
            ->askQuestion(anInstanceOf(BodyQuestion::class))
            ->andReturn(new Body('this is a commit body'));

        $console
            ->expects()
            ->askQuestion(anInstanceOf(HasBreakingChangesQuestion::class))
            ->andReturnTrue();

        $console
            ->expects()
            ->askQuestion(anInstanceOf(DescribeBreakingChangesQuestion::class))
            ->andReturn(new Footer('BREAKING CHANGE', 'something broke'));

        $console
            ->expects()
            ->askQuestion(anInstanceOf(AffectsOpenIssuesQuestion::class))
            ->andReturnTrue();

        $console
            ->expects()
            ->askQuestion(anInstanceOf(IssueTypeQuestion::class))
            ->times(3)
            ->andReturn('fix', 're', null);

        $console
            ->expects()
            ->askQuestion(anInstanceOf(IssueIdentifierQuestion::class))
            ->twice()
            ->andReturn(
                new Footer('fix', '1234', ' #'),
                new Footer('re', '4321', ' #'),
            );

        $console
            ->expects()
            ->askQuestion(anInstanceOf(AddFootersQuestion::class))
            ->andReturnTrue();

        $console
            ->expects()
            ->askQuestion(anInstanceOf(FooterTokenQuestion::class))
            ->times(3)
            ->andReturn('Signed-off-by', 'See-also', null);

        $console
            ->expects()
            ->askQuestion(anInstanceOf(FooterValueQuestion::class))
            ->twice()
            ->andReturn(
                new Footer('Signed-off-by', 'Janet Doe <jdoe@example.com>'),
                new Footer('See-also', 'abcdef0123456789'),
            );

        $console->expects()->section('Commit Message');
        $console->expects()->block($expectedMessage);

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $command = new PrepareCommand($factory);
        $command->run($input, $output);

        $this->assertInstanceOf(Message::class, $command->getMessage());
        $this->assertSame($expectedMessage, $command->getMessage()->toString());
    }

    public function testRunWithMinimalResponses(): void
    {
        $expectedMessage = <<<'EOD'
            feat: this is a commit summary

            EOD;

        // Fix line endings in case running tests on Windows.
        $expectedMessage = preg_replace('/(?<!\r)\n/', PHP_EOL, $expectedMessage);

        $input = new StringInput('');
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Prepare Commit Message');
        $console->expects()->text([
            'The following prompts will help you create a commit message that',
            'follows the <href=https://www.conventionalcommits.org/en/v1.0.0/>Conventional Commits</> specification.',
        ]);

        $console
            ->expects()
            ->askQuestion(anInstanceOf(TypeQuestion::class))
            ->andReturn(new Type('feat'));

        $console
            ->expects()
            ->askQuestion(anInstanceOf(ScopeQuestion::class))
            ->andReturnNull();

        $console
            ->expects()
            ->askQuestion(anInstanceOf(DescriptionQuestion::class))
            ->andReturn(new Description('this is a commit summary'));

        $console
            ->expects()
            ->askQuestion(anInstanceOf(BodyQuestion::class))
            ->andReturnNull();

        $console
            ->expects()
            ->askQuestion(anInstanceOf(HasBreakingChangesQuestion::class))
            ->andReturnFalse();

        $console
            ->expects()
            ->askQuestion(anInstanceOf(AffectsOpenIssuesQuestion::class))
            ->andReturnFalse();

        $console
            ->expects()
            ->askQuestion(anInstanceOf(AddFootersQuestion::class))
            ->andReturnFalse();

        $console->expects()->section('Commit Message');
        $console->expects()->block($expectedMessage);

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $command = new PrepareCommand($factory);
        $command->run($input, $output);

        $this->assertInstanceOf(Message::class, $command->getMessage());
        $this->assertSame($expectedMessage, $command->getMessage()->toString());
    }
}
