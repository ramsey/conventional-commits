<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Command;

use Hamcrest\Core\IsInstanceOf;
use Mockery\MockInterface;
use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
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
use Ramsey\Test\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

use function preg_replace;
use function realpath;
use function str_replace;

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
            ->askQuestion(new IsInstanceOf(TypeQuestion::class))
            ->andReturn(new Type('feat'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(ScopeQuestion::class))
            ->andReturn(new Scope('component'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(DescriptionQuestion::class))
            ->andReturn(new Description('this is a commit summary'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(BodyQuestion::class))
            ->andReturn(new Body('this is a commit body'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(HasBreakingChangesQuestion::class))
            ->andReturnTrue();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(DescribeBreakingChangesQuestion::class))
            ->andReturn(new Footer('BREAKING CHANGE', 'something broke'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(AffectsOpenIssuesQuestion::class))
            ->andReturnTrue();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(IssueTypeQuestion::class))
            ->times(3)
            ->andReturn('fix', 're', null);

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(IssueIdentifierQuestion::class))
            ->twice()
            ->andReturn(
                new Footer('fix', '1234', ' #'),
                new Footer('re', '4321', ' #'),
            );

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(AddFootersQuestion::class))
            ->andReturnTrue();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(FooterTokenQuestion::class))
            ->times(3)
            ->andReturn('Signed-off-by', 'See-also', null);

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(FooterValueQuestion::class))
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

        $configFile = (string) realpath(__DIR__ . '/../../../configs/default.json');

        // Windows-proof the file path.
        $configFile = str_replace('\\', '\\\\', $configFile);

        $input = new StringInput("--config=\"{$configFile}\"");
        $output = new NullOutput();

        $console = $this->mockery(SymfonyStyle::class);

        $console->expects()->title('Prepare Commit Message');
        $console->expects()->text([
            'The following prompts will help you create a commit message that',
            'follows the <href=https://www.conventionalcommits.org/en/v1.0.0/>Conventional Commits</> specification.',
        ]);

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(TypeQuestion::class))
            ->andReturn(new Type('feat'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(ScopeQuestion::class))
            ->andReturnNull();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(DescriptionQuestion::class))
            ->andReturn(new Description('this is a commit summary'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(BodyQuestion::class))
            ->andReturnNull();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(HasBreakingChangesQuestion::class))
            ->andReturnFalse();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(AffectsOpenIssuesQuestion::class))
            ->andReturnFalse();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(AddFootersQuestion::class))
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

    public function testRunChecksForRequiredFooters(): void
    {
        $expectedMessage = <<<'EOD'
            fix: this is a commit message

            Signed-off-by: Janet Doe <jdoe@example.com>
            See-also: https://example.com/foo
            Foo-bar: some footer value

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
            ->askQuestion(new IsInstanceOf(TypeQuestion::class))
            ->andReturn(new Type('fix'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(ScopeQuestion::class))
            ->andReturnNull();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(DescriptionQuestion::class))
            ->andReturn(new Description('this is a commit message'));

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(BodyQuestion::class))
            ->andReturnNull();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(HasBreakingChangesQuestion::class))
            ->andReturnFalse();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(AffectsOpenIssuesQuestion::class))
            ->andReturnFalse();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(AddFootersQuestion::class))
            ->never();

        $console
            ->expects()
            ->error('Please provide the following required footers: see-also, signed-off-by.');

        $console
            ->expects()
            ->error('Please provide the following required footers: see-also.');

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(FooterTokenQuestion::class))
            ->times(6)
            ->andReturn(null, 'Signed-off-by', null, 'See-also', 'Foo-bar', null);

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(FooterValueQuestion::class))
            ->times(3)
            ->andReturn(
                new Footer('Signed-off-by', 'Janet Doe <jdoe@example.com>'),
                new Footer('See-also', 'https://example.com/foo'),
                new Footer('Foo-bar', 'some footer value'),
            );

        $console->expects()->section('Commit Message');
        $console->expects()->block($expectedMessage);

        /** @var SymfonyStyleFactory & MockInterface $factory */
        $factory = $this->mockery(SymfonyStyleFactory::class);
        $factory->expects()->factory($input, $output)->andReturn($console);

        $configuration = new DefaultConfiguration([
            'requiredFooters' => ['See-also', 'Signed-off-by'],
        ]);

        $command = new PrepareCommand($factory);
        $command->setConfiguration($configuration);

        $command->run($input, $output);

        $this->assertInstanceOf(Message::class, $command->getMessage());
        $this->assertSame($expectedMessage, $command->getMessage()->toString());
    }
}
