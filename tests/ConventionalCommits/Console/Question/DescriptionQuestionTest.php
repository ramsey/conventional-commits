<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\DescriptionQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\Test\RamseyTestCase;

class DescriptionQuestionTest extends RamseyTestCase
{
    public function testQuestion(): void
    {
        $question = new DescriptionQuestion();

        $this->assertSame(
            'Write a short description of the change',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsFooter(): void
    {
        $question = new DescriptionQuestion();
        $validator = $question->getValidator();

        /** @var Description $description */
        $description = $validator('this is a description');

        $this->assertInstanceOf(Description::class, $description);
        $this->assertSame('this is a description', $description->toString());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new DescriptionQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid description. Please try again.');

        $validator(null);
    }
}
