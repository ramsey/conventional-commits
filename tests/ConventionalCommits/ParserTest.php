<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits;

use Ramsey\ConventionalCommits\Exception\InvalidCommitMessage;
use Ramsey\ConventionalCommits\Parser;
use Ramsey\Dev\Tools\TestCase;
use Ramsey\Test\SnapshotsTool;
use Ramsey\Test\WindowsSafeTextDriver;

use function file_get_contents;
use function preg_replace;
use function realpath;

use const PHP_EOL;

class ParserTest extends TestCase
{
    use SnapshotsTool;

    /**
     * @return array<array{rawMessageFile: string}>
     */
    public function provideRawCommitMessage(): array
    {
        return [
            'a basic commit' => [
                'rawMessageFile' => (string) realpath(__DIR__ . '/commit-messages/commit-message-00.txt'),
            ],
            'a full commit' => [
                'rawMessageFile' => (string) realpath(__DIR__ . '/commit-messages/commit-message-01.txt'),
            ],
            'with body and no footers' => [
                'rawMessageFile' => (string) realpath(__DIR__ . '/commit-messages/commit-message-02.txt'),
            ],
            'with footers and no body' => [
                'rawMessageFile' => (string) realpath(__DIR__ . '/commit-messages/commit-message-03.txt'),
            ],
            'with simple body and single footer' => [
                'rawMessageFile' => (string) realpath(__DIR__ . '/commit-messages/commit-message-04.txt'),
            ],
            'with breaking change and no body adds bang' => [
                'rawMessageFile' => (string) realpath(__DIR__ . '/commit-messages/commit-message-05.txt'),
            ],
            'with breaking change and body adds bang' => [
                'rawMessageFile' => (string) realpath(__DIR__ . '/commit-messages/commit-message-06.txt'),
            ],
        ];
    }

    /**
     * @dataProvider provideRawCommitMessage
     */
    public function testParserAccuratelyParsesCommitMessages(string $rawMessageFile): void
    {
        $rawMessage = (string) file_get_contents($rawMessageFile);

        // Fix line endings in case running tests on Windows.
        $rawMessage = (string) preg_replace('/(?<!\r)\n/', PHP_EOL, $rawMessage);

        $parser = new Parser();
        $commit = $parser->parse($rawMessage);

        $this->assertMatchesSnapshot($commit->toString(), new WindowsSafeTextDriver());
    }

    /**
     * @return array<array{invalidMessageFile: string}>
     */
    public function provideInvalidCommitMessage(): array
    {
        return [
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-00.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-01.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-02.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-03.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-04.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-05.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-06.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-07.txt')],
            ['invalidMessageFile' => (string) realpath(__DIR__ . '/commit-messages/invalid-commit-message-08.txt')],
        ];
    }

    /**
     * @dataProvider provideInvalidCommitMessage
     */
    public function testInvalidCommitMessageThrowsException(string $invalidMessageFile): void
    {
        $invalidMessage = (string) file_get_contents($invalidMessageFile);

        // Fix line endings in case running tests on Windows.
        $invalidMessage = (string) preg_replace('/(?<!\r)\n/', PHP_EOL, $invalidMessage);

        $parser = new Parser();

        $this->expectException(InvalidCommitMessage::class);
        $this->expectExceptionMessage('Could not find a valid Conventional Commits message');

        $parser->parse($invalidMessage);
    }
}
