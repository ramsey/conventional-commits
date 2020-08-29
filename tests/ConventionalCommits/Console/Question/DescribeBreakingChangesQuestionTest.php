<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\DescribeBreakingChangesQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\Dev\Tools\TestCase;

class DescribeBreakingChangesQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new DescribeBreakingChangesQuestion();

        $this->assertSame(
            'Describe the breaking changes',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsFooter(): void
    {
        $question = new DescribeBreakingChangesQuestion();
        $validator = $question->getValidator();

        /** @var Footer $footer */
        $footer = $validator('these are breaking changes');

        $this->assertInstanceOf(Footer::class, $footer);
        $this->assertSame('BREAKING CHANGE', $footer->getToken());
        $this->assertSame('these are breaking changes', $footer->getValue());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new DescribeBreakingChangesQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid description. Please try again.');

        $validator(null);
    }
}
