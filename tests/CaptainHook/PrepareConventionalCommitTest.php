<?php

declare(strict_types=1);

namespace Ramsey\Test\CaptainHook;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ActionConfig;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hooks;
use Mockery\MockInterface;
use Ramsey\CaptainHook\Input;
use Ramsey\CaptainHook\Output;
use Ramsey\CaptainHook\PrepareConventionalCommit;
use Ramsey\ConventionalCommits\Console\Command\PrepareCommand;
use Ramsey\ConventionalCommits\Message;
use Ramsey\Dev\Tools\TestCase;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

use function anInstanceOf;

class PrepareConventionalCommitTest extends TestCase
{
    public function testGetRestriction(): void
    {
        $this->assertTrue(
            PrepareConventionalCommit::getRestriction()->isApplicableFor(Hooks::PREPARE_COMMIT_MSG),
        );
    }

    public function testExecuteDoesNothingIfNotInteractiveMode(): void
    {
        /** @var PrepareCommand & MockInterface $prepareCommand */
        $prepareCommand = $this->mockery(PrepareCommand::class);

        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class, [
            'isInteractive' => false,
        ]);

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class);

        /** @var ActionConfig & MockInterface $actionConfig */
        $actionConfig = $this->mockery(ActionConfig::class);

        $action = new PrepareConventionalCommit($prepareCommand);

        $action->execute($config, $io, $repository, $actionConfig);
    }

    public function testExecuteDoesNothingIfCommitMsgAlreadyExists(): void
    {
        /** @var PrepareCommand & MockInterface $prepareCommand */
        $prepareCommand = $this->mockery(PrepareCommand::class);

        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class, [
            'isInteractive' => true,
        ]);

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class, [
            'getCommitMsg' => new CommitMessage('existing commit message'),
        ]);

        /** @var ActionConfig & MockInterface $actionConfig */
        $actionConfig = $this->mockery(ActionConfig::class);

        $action = new PrepareConventionalCommit($prepareCommand);

        $action->execute($config, $io, $repository, $actionConfig);
    }

    public function testExecuteDoesNothingIfPrepareCommandFailsToCreateMessage(): void
    {
        /** @var PrepareCommand & MockInterface $prepareCommand */
        $prepareCommand = $this->mockery(PrepareCommand::class);

        $prepareCommand
            ->expects()
            ->run(
                anInstanceOf(Input::class),
                anInstanceOf(Output::class),
            );

        $prepareCommand->expects()->getMessage()->andReturnNull();

        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class, [
            'isInteractive' => true,
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => false,
        ]);

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class, [
            'getCommitMsg' => new CommitMessage(''),
        ]);

        /** @var ActionConfig & MockInterface $actionConfig */
        $actionConfig = $this->mockery(ActionConfig::class);

        $action = new PrepareConventionalCommit($prepareCommand);

        $action->execute($config, $io, $repository, $actionConfig);
    }

    public function testExecuteSetsCommitMsg(): void
    {
        /** @var Message & MockInterface $message */
        $message = $this->mockery(Message::class, [
            'toString' => 'this is a commit message',
        ]);

        /** @var PrepareCommand & MockInterface $prepareCommand */
        $prepareCommand = $this->mockery(PrepareCommand::class);

        $prepareCommand
            ->expects()
            ->run(
                anInstanceOf(Input::class),
                anInstanceOf(Output::class),
            );

        $prepareCommand->expects()->getMessage()->andReturn($message);

        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class, [
            'isInteractive' => true,
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => false,
        ]);

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class, [
            'getCommitMsg' => new CommitMessage(''),
        ]);

        $repository
            ->expects()
            ->setCommitMsg(anInstanceOf(CommitMessage::class))
            ->andReturnUsing(function (CommitMessage $commitMessage): void {
                $this->assertSame(
                    'this is a commit message',
                    $commitMessage->getRawContent(),
                );
            });

        /** @var ActionConfig & MockInterface $actionConfig */
        $actionConfig = $this->mockery(ActionConfig::class);

        $action = new PrepareConventionalCommit($prepareCommand);

        $action->execute($config, $io, $repository, $actionConfig);
    }
}
