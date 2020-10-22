<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Console\Question\BodyQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\Dev\Tools\TestCase;

class BodyQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new BodyQuestion();

        $this->assertSame(
            'You may provide a longer description of the change',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsNullForEmptyString(): void
    {
        $question = new BodyQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(' '));
    }

    public function testValidatorReturnsNullForNull(): void
    {
        $question = new BodyQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(null));
    }

    public function testValidatorReturnsBody(): void
    {
        $question = new BodyQuestion();
        $validator = $question->getValidator();

        /** @var Body $body */
        $body = $validator('this is a body');

        $this->assertInstanceOf(Body::class, $body);
        $this->assertSame('this is a body', $body->toString());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new BodyQuestion(new DefaultConfiguration([
            'bodyRequired' => true,
        ]));

        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid body. You must provide a body.');

        $validator(null);
    }
}
