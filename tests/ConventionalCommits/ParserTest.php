<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits;

use Ramsey\ConventionalCommits\Exception\InvalidCommitMessage;
use Ramsey\ConventionalCommits\Parser;
use Ramsey\Test\RamseyTestCase;

class ParserTest extends RamseyTestCase
{
    /**
     * @return array<array{rawMessageFile: string, expectedMessageFile: string}>
     */
    public function provideRawAndExpectedCommitMessage(): array
    {
        return [
            'a basic commit' => [
                'rawMessageFile' => __DIR__ . '/../mocks/commit-message-00-raw.txt',
                'expectedMessageFile' => __DIR__ . '/../mocks/commit-message-00-expected.txt',
            ],
            'a full commit' => [
                'rawMessageFile' => __DIR__ . '/../mocks/commit-message-01-raw.txt',
                'expectedMessageFile' => __DIR__ . '/../mocks/commit-message-01-expected.txt',
            ],
            'with body and no footers' => [
                'rawMessageFile' => __DIR__ . '/../mocks/commit-message-02-raw.txt',
                'expectedMessageFile' => __DIR__ . '/../mocks/commit-message-02-expected.txt',
            ],
            'with footers and no body' => [
                'rawMessageFile' => __DIR__ . '/../mocks/commit-message-03-raw.txt',
                'expectedMessageFile' => __DIR__ . '/../mocks/commit-message-03-expected.txt',
            ],
            'with simple body and single footer' => [
                'rawMessageFile' => __DIR__ . '/../mocks/commit-message-04-raw.txt',
                'expectedMessageFile' => __DIR__ . '/../mocks/commit-message-04-expected.txt',
            ],
            'with breaking change and no body adds bang' => [
                'rawMessageFile' => __DIR__ . '/../mocks/commit-message-05-raw.txt',
                'expectedMessageFile' => __DIR__ . '/../mocks/commit-message-05-expected.txt',
            ],
            'with breaking change and body adds bang' => [
                'rawMessageFile' => __DIR__ . '/../mocks/commit-message-06-raw.txt',
                'expectedMessageFile' => __DIR__ . '/../mocks/commit-message-06-expected.txt',
            ],
        ];
    }

    /**
     * @dataProvider provideRawAndExpectedCommitMessage
     */
    public function testParserAccuratelyParsesCommitMessages(
        string $rawMessageFile,
        string $expectedMessageFile
    ): void {
        $rawMessage = (string) file_get_contents($rawMessageFile);
        $expectedMessage = (string) file_get_contents($expectedMessageFile);

        $parser = new Parser();
        $commit = $parser->parse($rawMessage);

        $this->assertSame($expectedMessage, $commit->toString());
    }

    /**
     * @return array<array{invalidMessageFile: string}>
     */
    public function provideInvalidCommitMessage(): array
    {
        return [
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-00.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-01.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-02.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-03.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-04.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-05.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-06.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-07.txt'],
            ['invalidMessageFile' => __DIR__ . '/../mocks/invalid-commit-message-08.txt'],
        ];
    }

    /**
     * @dataProvider provideInvalidCommitMessage
     */
    public function testInvalidCommitMessageThrowsException(string $invalidMessageFile): void
    {
        $invalidMessage = (string) file_get_contents($invalidMessageFile);
        $parser = new Parser();

        $this->expectException(InvalidCommitMessage::class);
        $this->expectExceptionMessage('Could not find a valid Conventional Commits message');

        $parser->parse($invalidMessage);
    }
}
