<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\BodyQuestion;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\Test\RamseyTestCase;

class BodyQuestionTest extends RamseyTestCase
{
    public function testQuestion(): void
    {
        $question = new BodyQuestion();

        $this->assertSame(
            'You may provide a longer description of the change <comment>(press enter to skip)</comment>',
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
}
