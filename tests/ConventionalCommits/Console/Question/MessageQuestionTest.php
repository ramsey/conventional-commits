<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\MessageQuestion;
use Ramsey\Test\TestCase;

class MessageQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new MessageQuestion();

        $this->assertSame(
            'Enter the commit message to be validated',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsNullForEmptyString(): void
    {
        $question = new MessageQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(' '));
    }

    public function testValidatorReturnsNullForNull(): void
    {
        $question = new MessageQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(null));
    }

    public function testValidatorReturnsBody(): void
    {
        $question = new MessageQuestion();
        $validator = $question->getValidator();

        /** @var string $message */
        $message = $validator('this is a message');

        $this->assertSame('this is a message', $message);
    }
}
