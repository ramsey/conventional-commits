<?php

declare(strict_types=1);

namespace Ramsey\Test\CaptainHook;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ConfigAction;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use Mockery\MockInterface;
use Ramsey\CaptainHook\ConventionalCommits;
use Ramsey\Test\RamseyTestCase;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

class ConventionalCommitsTest extends RamseyTestCase
{
    public function testGetRestriction(): void
    {
        $restriction = ConventionalCommits::getRestriction();

        $this->assertTrue($restriction->isApplicableFor('commit-msg'));
    }

    public function testExecute(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class);

        /** @var ConfigAction & MockInterface $configAction */
        $configAction = $this->mockery(ConfigAction::class);

        /** @var CommitMessage & MockInterface $commitMessage */
        $commitMessage = $this->mockery(CommitMessage::class);
        $commitMessage
            ->expects()
            ->getContent()
            ->andReturn('feat: implementing something real nice');

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class);
        $repository
            ->expects()
            ->getCommitMsg()
            ->andReturn($commitMessage);

        $action = new ConventionalCommits();

        $action->execute($config, $io, $repository, $configAction);
    }

    public function testExecuteThrowsException(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class);

        /** @var ConfigAction & MockInterface $configAction */
        $configAction = $this->mockery(ConfigAction::class);

        /** @var CommitMessage & MockInterface $commitMessage */
        $commitMessage = $this->mockery(CommitMessage::class);
        $commitMessage
            ->expects()
            ->getContent()
            ->andReturn('not a valid commit message');

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class);
        $repository
            ->expects()
            ->getCommitMsg()
            ->andReturn($commitMessage);

        $action = new ConventionalCommits();

        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage(
            'The commit message is not properly formatted according '
            . 'to the Conventional Commits specification',
        );

        $action->execute($config, $io, $repository, $configAction);
    }
}
