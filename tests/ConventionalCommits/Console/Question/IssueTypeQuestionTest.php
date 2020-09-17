<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\IssueTypeQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\Dev\Tools\TestCase;

class IssueTypeQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new IssueTypeQuestion();

        $this->assertSame(
            'What is the issue reference type? (e.g., fix, re)',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsNullForEmptyString(): void
    {
        $question = new IssueTypeQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(' '));
    }

    public function testValidatorReturnsNullForNull(): void
    {
        $question = new IssueTypeQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(null));
    }

    public function testValidatorReturnsTokenString(): void
    {
        $question = new IssueTypeQuestion();
        $validator = $question->getValidator();

        /** @var string $type */
        $type = $validator('re');

        $this->assertSame('re', $type);
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new IssueTypeQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid issue reference type. Token \'fix re\' is invalid.');

        $validator('fix re');
    }
}
