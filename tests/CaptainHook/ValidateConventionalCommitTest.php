<?php

declare(strict_types=1);

namespace Ramsey\Test\CaptainHook;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ConfigAction;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use Mockery\MockInterface;
use Ramsey\CaptainHook\ValidateConventionalCommit;
use Ramsey\Dev\Tools\TestCase;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;
use Throwable;

use function trim;

class ValidateConventionalCommitTest extends TestCase
{
    public function testGetRestriction(): void
    {
        $restriction = ValidateConventionalCommit::getRestriction();

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

        $action = new ValidateConventionalCommit();

        $action->execute($config, $io, $repository, $configAction);
    }

    public function testExecuteThrowsException(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        $output = '';

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class, [
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => false,
        ]);

        $io->shouldReceive('write')->andReturnUsing(
            function (string $value) use (&$output): void {
                if (trim($value) !== '') {
                    $output .= trim($value) . ' ';
                }
            },
        );

        /** @var ConfigAction & MockInterface $configAction */
        $configAction = $this->mockery(ConfigAction::class);

        /** @var CommitMessage & MockInterface $commitMessage */
        $commitMessage = $this->mockery(CommitMessage::class);
        $commitMessage
            ->expects()
            ->getContent()
            ->once()
            ->andReturn('not a valid commit message');

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class);
        $repository
            ->expects()
            ->getCommitMsg()
            ->andReturn($commitMessage);

        $action = new ValidateConventionalCommit();

        $exception = null;

        try {
            $action->execute($config, $io, $repository, $configAction);
        } catch (Throwable $exception) {
            // Do nothing.
        }

        // We do not use the expectException() or expectExceptionMessage()
        // assertions here because we want to assert the content of $output.
        $this->assertInstanceOf(ActionFailed::class, $exception);
        $this->assertSame('Validation failed', $exception->getMessage());
        $this->assertSame(
            '[ERROR] Invalid Commit Message The commit message is '
            . 'not properly formatted according to the Conventional '
            . 'Commits specification. For more details, see '
            . 'https://www.conventionalcommits.org/en/v1.0.0/',
            trim($output),
        );
    }
}
