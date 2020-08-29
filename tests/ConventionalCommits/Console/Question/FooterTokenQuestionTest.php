<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\FooterTokenQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\Dev\Tools\TestCase;

class FooterTokenQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new FooterTokenQuestion();

        $this->assertSame(
            'What is the name of the footer? (e.g., Signed-off-by, See-also) '
            . '<comment>(press enter to continue)</comment>',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsNullForEmptyString(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(' '));
    }

    public function testValidatorReturnsNullForNull(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(null));
    }

    public function testValidatorReturnsTokenString(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        /** @var string $token */
        $token = $validator('token');

        $this->assertSame('token', $token);
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid footer name. Please try again.');

        $validator('invalid token');
    }
}
